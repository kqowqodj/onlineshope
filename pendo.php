<?php
// Novus by Pendo web SDK. Include this inside the <head> of every page that
// renders HTML: each page is a full page load, so Pendo is initialized once
// per page. The app has no sign-in, so visitors are anonymous; an empty
// visitor id lets the agent reuse the id it saved for this browser, or
// create a new one.
?>
<script>
(function(apiKey){
    (function(p,e,n,d,o){var v,w,x,y,z;o=p[d]=p[d]||{};o._q=o._q||[];
    v=['initialize','identify','updateOptions','pageLoad','track', 'trackAgent'];for(w=0,x=v.length;w<x;++w)(function(m){
    o[m]=o[m]||function(){o._q[m===v[0]?'unshift':'push']([m].concat([].slice.call(arguments,0)));};})(v[w]);
    y=e.createElement(n);y.async=!0;y.src='https://cdn.pendo.io/agent/static/'+apiKey+'/pendo.js';
    z=e.getElementsByTagName(n)[0];z.parentNode.insertBefore(y,z);})(window,document,'script','pendo');
})('fb110c63-b428-4253-888e-fde8fdc2f60a');
</script>
<script>
    pendo.initialize({
        visitor: {
            id: ''
        }
    });
</script>
