function backWithRefresh(){window.location.href;history.go(-2),setTimeout(function(){historyPush("",history,!1),location.reload()},100)}function historyPush(t,s,e){e&&(e.pushingState=!0);let n=window.location.href,i=!1;"list-1"==t&&(-1!==(l=n.indexOf("?"))&&(n=n.substr(0,l)),i=!0);var o=n.indexOf("?list-1");-1!==o&&(n=n.substr(0,o));var u="?"+t,l=n.indexOf("?state="),o=n.indexOf("?"+t);-1!==l?n=n.substr(0,l):-1!==o&&(n=n.substr(0,o));let h="";-1!==n.indexOf("?")?(h=u,l=n.indexOf("&state="),o=n.indexOf("&"+t),-1!==l?n=n.substr(0,l):-1!==o&&(n=n.substr(0,o))):h=u,document.location.hash?s.pushState(null,document.title,n+h+document.location.hash):s.pushState(null,document.title,n+h),i&&("list-1"!=t||-1!==(t=n.indexOf("?list-1"))&&(n=n.substr(0,t)),document.location.hash?s.pushState(null,document.title,n+document.location.hash):s.pushState(null,document.title,n)),e&&(e.pushingState=!1)}