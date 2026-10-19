<?php
/** @var CMain $APPLICATION */

if (!check_bitrix_sessid())
{
	return;
}

?>
<form action="<?= $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
	<input type="submit" value="<?= GetMessage('MOD_BACK') ?>">
</form>
