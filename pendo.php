<?php
// Novus by Pendo web SDK, included once in the <head> of every HTML page.
// It loads the agent and initializes Pendo once per page load; the empty
// visitor id starts an anonymous visitor (reused from cookies/localStorage
// on return). If sign-in is added later, call pendo.identify after login and
// pendo.clearSession on logout rather than initializing a second time.
?>
<script>
(function(apiKey){
    (function(p,e,n,d,o){var v,w,x,y,z;o=p[d]=p[d]||{};o._q=o._q||[];
    v=['initialize','identify','updateOptions','pageLoad','track', 'trackAgent'];for(w=0,x=v.length;w<x;++w)(function(m){
    o[m]=o[m]||function(){o._q[m===v[0]?'unshift':'push']([m].concat([].slice.call(arguments,0)));};})(v[w]);
    y=e.createElement(n);y.async=!0;y.src='https://cdn.pendo.io/agent/static/'+apiKey+'/pendo.js';
    z=e.getElementsByTagName(n)[0];z.parentNode.insertBefore(y,z);})(window,document,'script','pendo');
})('d8c1a851-1fc7-430a-8a4b-e33712d7be38');
</script>
<script>
    pendo.initialize({
        visitor: {
            id: ''
        }
    });
</script>
