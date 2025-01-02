<?php

/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

header('Content-Type: text/javascript');
switch ($_GET['lang']) {
    case "de":
        $lang = [
                'bold' => 'Fett',
                'italic' => 'Kursiv',
                'strikethrough' => 'Durchgestrichen',
                'href' => 'Link',
                'h2' => 'Überschrift 2',
                'h3' => 'Überschrift 3',
                'quote' => 'Zitat',
                'code' => 'Code',
                'bullets' => 'Unsortierte Liste',
                'numbers' => 'Nummerierte Liste',
                'decrease_level' => 'Einzug verkleinern',
                'increase_level' => 'Einzug erweitern',
                'attach' => 'Datei anhängen',
                'undo' => 'Rückgängig',
                'redo' => 'Wiederholen',
                'url' => 'URL',
                'url_placeholder' => 'URL',
                'link' => 'Link hinzufügen',
                'unlink' => 'Link entfernen'
        ];
        break;
    default:
        $lang = [
            'bold' => 'Bold',
            'italic' => 'Italic',
            'strikethrough' => 'Strikethrough',
            'href' => 'Link',
            'h2' => 'Headline 2',
            'h3' => 'Headline 3',
            'quote' => 'Quote',
            'code' => 'Code',
            'bullets' => 'Unordered List',
            'numbers' => 'Numbered List',
            'decrease_level' => 'Decrease Level',
            'increase_level' => 'Increase Level',
            'attach' => 'Attach File',
            'undo' => 'Undo',
            'redo' => 'Redo',
            'url' => 'URL',
            'url_placeholder' => 'URL',
            'link' => 'Link',
            'unlink' => 'Unlink'
        ];
        break;
}
?>

function trixConfig() {
    if (typeof Trix !== 'undefined') {
        delete Trix.config.blockAttributes.heading1;

        <?php if ($_GET['h2'] === '1') {?>
            Trix.config.blockAttributes.heading2 = {
            tagName: "h2",
            terminal: true,
            breakOnReturn: true,
            group: false
            };
        <?php }?>

        <?php if ($_GET['h3'] === '1') {?>
            Trix.config.blockAttributes.heading3 = {
            tagName: "h3",
            terminal: true,
            breakOnReturn: true,
            group: false
            };
        <?php }?>

        <?php if ($_GET['attach'] !== '1') {?>
            document.addEventListener("trix-file-accept", event => {
            event.preventDefault()
            })
        <?php } else {?>
            (function() {

            addEventListener('trix-attachment-add', function(event) {
            if (event.attachment.file) {
            uploadFileAttachment(event.attachment);
            }
            });

            function uploadFileAttachment(attachment) {
            uploadFile(attachment.file, setProgress, setAttributes);

            function setProgress(progress) {
            attachment.setUploadProgress(progress);
            }

            function setAttributes(attributes) {
            attachment.setAttributes(attributes);
            }
            }

            function uploadFile(file, progressCallback, successCallback) {
            let key = createStorageKey(file);
            let formData = createFormData(key, file);
            let xhr = new XMLHttpRequest();
            let url;
            if (file.type.includes('image')) {
            url = '<?php echo $_GET['imageuploadpath'] ?: '/con4gis/upload/image'; ?>';
            } else {
            url = '<?php echo $_GET['fileuploadpath'] ?: '/con4gis/upload/file'; ?>';
            }

            xhr.open('POST', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function(event) {
            let progress = event.loaded / event.total * 100;
            progressCallback(progress);
            });

            xhr.addEventListener('load', function(event) {
            if (xhr.status === 200) {
            let response = JSON.parse(xhr.response);
            let attributes = {
            url: response.url,
            href: response.url
            };
            successCallback(attributes);
            }
            });

            xhr.send(formData);
            }

            function createStorageKey(file) {
            let date = new Date();
            let day = date.toISOString().slice(0,10);
            let name = date.getTime() + '-' + file.name;
            return ['tmp', day, name ].join('/');
            }

            function createFormData(key, file) {
            let data = new FormData();
            data.append('key', key);
            data.append('Content-Type', file.type);
            data.append('upload', file);
            return data;
            }
            })();
        <?php }?>

        <?php if ($_GET['href'] !== '1') {?>
            delete Trix.config.textAttributes.href;
        <?php }?>

        const {lang} = Trix.config;

        Trix.config.toolbar = {
        getDefaultHTML() { return `
        <div class="trix-button-row">
                      <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="<?php echo $lang['bold']; ?>" tabindex="-1"><?php echo $lang['bold']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="<?php echo $lang['italic']; ?>" tabindex="-1"><?php echo $lang['italic']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="<?php echo $lang['strikethrough']; ?>" tabindex="-1"><?php echo $lang['strikethrough']; ?></button>
                        <?php if ($_GET['href'] === '1') {?>
                            <button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" title="<?php echo $lang['href']; ?>" tabindex="-1"><?php echo $lang['href']; ?></button>
                        <?php }?>
                      </span>
            <span class="trix-button-group trix-button-group--block-tools" data-trix-button-group="block-tools">
                        <?php if ($_GET['h2'] === '1') {?>
                            <button type="button" class="trix-button" data-trix-attribute="heading2" title="<?php echo $lang['h2']; ?>" tabindex="-1">H2</button>
                        <?php }?>
                <?php if ($_GET['h3'] === '1') {?>
                    <button type="button" class="trix-button" data-trix-attribute="heading3" title="<?php echo $lang['h3']; ?>" tabindex="-1">H3</button>
                <?php }?>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-quote" data-trix-attribute="quote" title="<?php echo $lang['quote']; ?>" tabindex="-1"><?php echo $lang['quote']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-code" data-trix-attribute="code" title="<?php echo $lang['code']; ?>" tabindex="-1"><?php echo $lang['code']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="<?php echo $lang['bullets']; ?>" tabindex="-1"><?php echo $lang['bullets']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="<?php echo $lang['numbers']; ?>" tabindex="-1"><?php echo $lang['numbers']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-decrease-nesting-level" data-trix-action="decreaseNestingLevel" title="<?php echo $lang['decrease_level']; ?>" tabindex="-1"><?php echo $lang['decrease_level']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-increase-nesting-level" data-trix-action="increaseNestingLevel" title="<?php echo $lang['increase_level']; ?>" tabindex="-1"><?php echo $lang['increase_level']; ?></button>
                      </span>
            <?php if ($_GET['attach'] === '1') {?>
                <span class="trix-button-group trix-button-group--file-tools" data-trix-button-group="file-tools">
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-attach" data-trix-action="attachFiles" title="<?php echo $lang['attach']; ?>" tabindex="-1"><?php echo $lang['attach']; ?></button>
                      </span>
            <?php }?>
            <span class="trix-button-group-spacer"></span>
            <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="<?php echo $lang['undo']; ?>" tabindex="-1"><?php echo $lang['undo']; ?></button>
                        <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="<?php echo $lang['redo']; ?>" tabindex="-1"><?php echo $lang['redo']; ?></button>
                      </span>
        </div>
        <div class="trix-dialogs" data-trix-dialogs>
            <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
                <div class="trix-dialog__link-fields">
                    <input type="url" name="href" class="trix-input trix-input--dialog" placeholder="<?php echo $lang['url_placeholder']; ?>" aria-label="<?php echo $lang['url']; ?>" required data-trix-input>
                    <div class="trix-button-group">
                        <input type="button" class="trix-button trix-button--dialog" value="<?php echo $lang['link']; ?>" data-trix-method="setAttribute">
                        <input type="button" class="trix-button trix-button--dialog" value="<?php echo $lang['unlink']; ?>" data-trix-method="removeAttribute">
                    </div>
                </div>
            </div>
        </div>
        `; }
        };
    } else {
        window.setTimeout(trixConfig, 1);
    }
}

window.setTimeout(trixConfig, 1);