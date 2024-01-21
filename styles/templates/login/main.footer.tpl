<footer>
	<a href="https://discord.gg/jhYYN3yuat" title="Discord" target="copy">Discord</a> community server<br>
</footer>
</div>
<div id="dialog" style="display:none;"></div>
<script>
  var LoginConfig = {
    'isMultiUniverse': {$isMultiUniverse|json_encode},
    'unisWildcast': {$unisWildcast|json_encode},
    'referralEnable' : {$referralEnable|json_encode},
    'basePath' : {$basepath|json_encode}
  };
</script>
{if $analyticsEnable}
  <script type="text/javascript" src="http://www.google-analytics.com/ga.js"></script>
  <script type="text/javascript">
  try {
    var pageTracker = _gat._getTracker("{$analyticsUID}");
    pageTracker._trackPageview();
  } catch(err) { }
  </script>
{/if}
</body>
</html>
