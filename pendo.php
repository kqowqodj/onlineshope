<?php
// Novus by Pendo web SDK: install snippet + anonymous initialize.
// Include once in the <head> of every full HTML page so the SDK boots exactly
// once per page load. Don't initialize the SDK anywhere else: repeat
// initialize calls are silently ignored. There is no sign-in flow yet, so
// visitors are anonymous (an empty id reuses a returning visitor's anonymous
// id). If one is added, register the user with Pendo's identify method after
// sign-in and call its clearSession method on sign-out.
?>
<script>
(function(apiKey){
    (function(p,e,n,d,o){var v,w,x,y,z;o=p[d]=p[d]||{};o._q=o._q||[];
    v=['initialize','identify','updateOptions','pageLoad','track', 'trackAgent'];for(w=0,x=v.length;w<x;++w)(function(m){
    o[m]=o[m]||function(){o._q[m===v[0]?'unshift':'push']([m].concat([].slice.call(arguments,0)));};})(v[w]);
    y=e.createElement(n);y.async=!0;y.src='https://cdn.pendo.io/agent/static/'+apiKey+'/pendo.js';
    z=e.getElementsByTagName(n)[0];z.parentNode.insertBefore(y,z);})(window,document,'script','pendo');
})('031a176e-3b57-4998-b731-36a71886a297');
</script>
<script>
    pendo.initialize({
        visitor: {
            id: ''
        }
    });
</script>
