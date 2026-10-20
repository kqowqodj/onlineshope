<?php
// Novus by Pendo: loads the web SDK and initializes it once per page load.
// Include this file exactly once, inside <head>, on every page that renders HTML.
?>
<script>
(function(apiKey){
    (function(p,e,n,d,o){var v,w,x,y,z;o=p[d]=p[d]||{};o._q=o._q||[];
    v=['initialize','identify','updateOptions','pageLoad','track', 'trackAgent'];for(w=0,x=v.length;w<x;++w)(function(m){
    o[m]=o[m]||function(){o._q[m===v[0]?'unshift':'push']([m].concat([].slice.call(arguments,0)));};})(v[w]);
    y=e.createElement(n);y.async=!0;y.src='https://cdn.pendo.io/agent/static/'+apiKey+'/pendo.js';
    z=e.getElementsByTagName(n)[0];z.parentNode.insertBefore(y,z);})(window,document,'script','pendo');
})('eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhY2VudGVyIjoidXMiLCJrZXkiOiI2MzI5YWQxZDgyZjE5ZjRjMDE1NDM1YzBkN2NmNDU0NmZkYWJlMjdlOWEwZmQ3ZjMwNDMyYzk0YzY2ZGY1MDQ1OGY3NTU4YjI1YjJkNDZhOTk3NzNiYzhlODc5YmNkZjQ5NTk2NjMwMDIwMGMyNDJmNTU4NjNkYWE2OWQ0MDYzNzQwZjY4MGNmODBhMDEwNWRlZGJiODhjYWIxODFlZjg0OTllZWM5NmVjN2NiMjdkM2E3ZWQwOTQwNDcyN2UyM2QuYmFiMjFkNTE5ZWVmZmViYmQwMzY1MzhmNmM3NTUzMGYuYWU3ZDY0ODk0MTBhMWZjNjgyMmM2MDVhNWU0ZjE3NzM2ZThkNTI0MGI2ODc4Zjg2MDM4NTczMGU1MmMxZWQ2YSJ9.Lk-hR2pbAy-QokgSrg0Rs122rhJZlbVv2pZBdx-J4-9bXAya2gL30OitP97d6CnbHCY0Tr2rnnlqCis9Z6aP6djFg-7vvtK5QIzziQxFfECLxo1hTZerkSAh9FaqpwsjhHmcJUrf9dI2oCeF8GlQciXBsmt5Av8ZDFFrE1r2c50');
</script>
<script>
    // The app has no sign-in, so every visitor is anonymous: an empty id lets the SDK
    // reuse the visitor id it stored on an earlier visit, or create a new one.
    pendo.initialize({
        visitor: {
            id: ''
        }
    });
</script>
