{include file="overall_header.tpl"}
<script type="text/javascript">

function check(){
    var universeValue = $('#universe').val();
    $.post('admin.php?page=reshufflePlayers&action=send&universe='+universeValue, $('#message').serialize(), function(data) {
        Dialog.alert(data, function() {
            location.reload();
        });
    });
    return true;
}
</script>
<form name="message" id="message" action="admin.php?page=reshufflePlayers&action=send&ajax=1">
<table class="table569">
		<tr>
            <th colspan="2">{$LNG.rp_header}</th>
        </tr>
        <tr>
            <th colspan="2">{$LNG.rp_explain}</th>
        </tr>
        <tr>
            <td>{$LNG.uni_reg}</td>
            <td>{html_options name=universe options=$universes}</td>
		</tr>
        <tr>
            <td colspan="2"><input type="button" onclick="check();" value="{$LNG.button_submit}"></td>
        </tr>
    </table>
</form>
{include file="overall_footer.tpl"}
