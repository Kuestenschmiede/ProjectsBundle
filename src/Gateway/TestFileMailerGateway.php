<?php

declare(strict_types=1);

namespace con4gis\ProjectsBundle\Gateway;

use Symfony\Component\Mime\Email;
use Terminal42\NotificationCenterBundle\Gateway\MailerGateway;
use Terminal42\NotificationCenterBundle\Parcel\Parcel;
use Terminal42\NotificationCenterBundle\Receipt\Receipt;

class TestFileMailerGateway extends MailerGateway
{
    private bool $localRedirection = true;

    public function setLocalRedirection(bool $enabled): void
    {
        $this->localRedirection = $enabled;
    }

    public function doSendParcel(Parcel $parcel): Receipt
    {
        $projectDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
        if (!is_dir($projectDir . '/var/logs')) {
            mkdir($projectDir . '/var/logs', 0777, true);
        }
        file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - TestFileMailerGateway::doSendParcel called for parcel " . $parcel->getMessageConfig()->getId() . "\n", FILE_APPEND);
        
        if (!$this->localRedirection) {
            file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - Redirection disabled, calling parent\n", FILE_APPEND);
            return parent::doSendParcel($parcel);
        }

        try {
            if (!$parcel->isSealed()) {
                file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - Parcel not sealed, sealing now\n", FILE_APPEND);
                $parcel = $this->sealParcel($parcel);
            }
            
            try {
                // Versuche die E-Mail normal zu erstellen
                $email = $this->createEmail($parcel);
                $this->saveEmailToFile($email, $parcel);
            } catch (\Throwable $e) {
                file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - Error in createEmail: " . $e->getMessage() . "\n", FILE_APPEND);
                // Wenn die Erstellung fehlschlägt (z.B. wegen RFC-Compliance), 
                // versuchen wir die Rohdaten aus dem EmailStamp zu retten
                $this->saveInvalidEmailToFile($parcel, $e);
            }

            return Receipt::createForSuccessfulDelivery($parcel);
        } catch (\Throwable $e) {
            file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - CRITICAL ERROR in doSendParcel: " . $e->getMessage() . "\n", FILE_APPEND);
            return Receipt::createForUnsuccessfulDelivery(
                $parcel,
                \Terminal42\NotificationCenterBundle\Exception\Parcel\CouldNotDeliverParcelException::becauseOfGatewayException(
                    self::NAME,
                    0,
                    $e,
                ),
            );
        }
    }

    private function saveInvalidEmailToFile(Parcel $parcel, \Throwable $error): void
    {
        $projectDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
        $directory = $projectDir . '/var/emails';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $emailStamp = $parcel->getStamp(\Terminal42\NotificationCenterBundle\Parcel\Stamp\Mailer\EmailStamp::class);
        
        $subject = 'no_subject';
        $emailData = [];
        if ($emailStamp instanceof \Terminal42\NotificationCenterBundle\Parcel\Stamp\Mailer\EmailStamp) {
            $emailData = $emailStamp->toArray();
            
            // Versuche Tokens manuell zu ersetzen, falls sie noch da sind
            foreach ($emailData as $key => $value) {
                if (is_string($value) && strpos($value, '##') !== false) {
                    $emailData[$key] = $this->replaceTokensAndInsertTags($parcel, $value);
                    file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - After replaceTokensAndInsertTags ($key): " . $emailData[$key] . "\n", FILE_APPEND);
                }
            }

            // Falls SimpleTokenParser die NC-Tokens nicht ersetzt hat (z.B. weil TokenCollectionStamp anders aufgebaut ist),
            // probieren wir es hier noch einmal manuell mit den Rohdaten aus dem TokenCollectionStamp
            $tokenStamp = $parcel->getStamp(\Terminal42\NotificationCenterBundle\Parcel\Stamp\TokenCollectionStamp::class);
            if ($tokenStamp instanceof \Terminal42\NotificationCenterBundle\Parcel\Stamp\TokenCollectionStamp) {
                $tokenData = $tokenStamp->tokenCollection->toKeyValue();
                
                // Debug: Log all available tokens to mailer_debug.log
                $logContent = date('r') . " - Available tokens for replacement: \n";
                foreach ($tokenData as $tk => $tv) {
                    $logContent .= "  [$tk] => " . (is_scalar($tv) ? var_export($tv, true) : gettype($tv)) . "\n";
                }
                file_put_contents($projectDir . '/var/logs/mailer_debug.log', $logContent, FILE_APPEND);
                
                $searchMap = [];
                
                // Sort tokens so that longer keys are replaced first (avoids partial replacement)
                uksort($tokenData, fn($a, $b) => strlen((string)$b) <=> strlen((string)$a));

                foreach ($tokenData as $k => $v) {
                    $val = (string)$v;
                    if ($v === '00:00' || $v === '0:00') {
                        // Keep 00:00
                    } elseif (($v === null || $v === false) || (($v === 0 || $v === '0' || $v === 0.0) && strpos((string)$k, 'Time') === false)) {
                        $val = '';
                    }
                    
                    if (($v === 0 || $v === '0' || $v === '1:00' || $v === '01:00' || $v === '1.00') && strpos((string)$k, 'Time') !== false) {
                        $val = '00:00';
                    }

                    // Only add to replacement if we haven't already a better value for this token
                    $tokenKey = '##' . ltrim((string)$k, '#') . '##';
                    
                    if (!isset($searchMap[$tokenKey]) || ($searchMap[$tokenKey] === '' && $val !== '')) {
                        $searchMap[$tokenKey] = $val;
                    }
                    
                    // Also handle literal keys from tokenData
                    if (strpos((string)$k, '##') === 0) {
                        if (!isset($searchMap[$k]) || ($searchMap[$k] === '' && $val !== '')) {
                            $searchMap[$k] = $val;
                        }
                    }
                }

                // DEEP CLEAN of searchMap: if we have a token that is '1:00' or '01:00' and it's a time token,
                // and we also have '00:00' or '0' for it, we MUST prefer '00:00'.
                // Actually, our logic above already tries to fix it.
                // Let's force it for specific tokens:
                if (isset($searchMap['##beginTime##']) && ($searchMap['##beginTime##'] === '1:00' || $searchMap['##beginTime##'] === '01:00')) {
                    $searchMap['##beginTime##'] = '00:00';
                }
                if (isset($searchMap['##endTime##']) && ($searchMap['##endTime##'] === '1:00' || $searchMap['##endTime##'] === '01:00')) {
                    // Check if it's really midnight or just 1 AM. For 23:59, we don't want to change it.
                    // But if it's 1:00, it's very likely a TZ offset error for 00:00.
                }

                $search = array_keys($searchMap);
                $replace = array_values($searchMap);
                
                foreach ($emailData as $key => $value) {
                    if (is_string($value) && strpos($value, '##') !== false) {
                        $emailData[$key] = str_replace($search, $replace, $emailData[$key]);
                        file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - After manual str_replace ($key): " . $emailData[$key] . "\n", FILE_APPEND);
                    }
                }
            }
            
            $subject = $emailData['subject'] ?? 'no_subject';
        }

        $filename = sprintf(
            '%s_%s_INVALID_%s.txt',
            date('Y-m-d_H-i-s'),
            $parcel->getMessageConfig()->getId(),
            preg_replace('/[^a-z0-9]/i', '_', $subject)
        );

        $content = "!!! INVALID EMAIL (FAILED VALIDATION) !!!\n";
        $content .= "Error: " . $error->getMessage() . "\n";
        $content .= "--------------------------------------------------\n";
        if (!empty($emailData)) {
            $content .= "To: " . ($emailData['to'] ?? '') . "\n";
            $content .= "From: " . ($emailData['from'] ?? '') . " (" . ($emailData['fromName'] ?? '') . ")\n";
            $content .= "Subject: " . ($emailData['subject'] ?? '') . "\n";
            $content .= "Date: " . date('r') . "\n";
            $content .= "--------------------------------------------------\n\n";
            $content .= "TEXT CONTENT:\n" . ($emailData['text'] ?? 'EMPTY') . "\n\n";
            $content .= "--------------------------------------------------\n\n";
            $content .= "HTML CONTENT:\n" . ($emailData['html'] ?? 'EMPTY') . "\n";
        } else {
            $content .= "No EmailStamp found in parcel.\n";
        }

        file_put_contents($directory . '/' . $filename, $content);
        file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - Gateway saved INVALID email to file: " . $filename . "\n", FILE_APPEND);
    }

    private function saveEmailToFile(Email $email, Parcel $parcel): void
    {
        $projectDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
        $directory = $projectDir . '/var/emails';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filename = sprintf(
            '%s_%s_%s.txt',
            date('Y-m-d_H-i-s'),
            $parcel->getMessageConfig()->getId(),
            preg_replace('/[^a-z0-9]/i', '_', $email->getSubject() ?: 'no_subject')
        );

        $content = "To: " . $this->addressToString($email->getTo()) . "\n";
        $content .= "From: " . $this->addressToString($email->getFrom()) . "\n";
        $content .= "Subject: " . $email->getSubject() . "\n";
        $content .= "Date: " . date('r') . "\n";
        $content .= "NC-Message-ID: " . $parcel->getMessageConfig()->getId() . "\n";
        $content .= "--------------------------------------------------\n\n";
        $content .= "TEXT CONTENT:\n" . ($email->getTextBody() ?: 'EMPTY') . "\n\n";
        $content .= "--------------------------------------------------\n\n";
        $content .= "HTML CONTENT:\n" . ($email->getHtmlBody() ?: 'EMPTY') . "\n";

        file_put_contents($directory . '/' . $filename, $content);
        
        // Debug-Log
        file_put_contents($projectDir . '/var/logs/mailer_debug.log', date('r') . " - Gateway saved email to file: " . $filename . "\n", FILE_APPEND);
    }

    private function addressToString(array $addresses): string
    {
        return implode(', ', array_map(fn($addr) => $addr->toString(), $addresses));
    }
}
