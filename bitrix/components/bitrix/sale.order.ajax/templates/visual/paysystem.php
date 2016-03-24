<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="section">
<div class="title"><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></div>

<table class="sale_order_table paysystem">
	<?
	if ($arResult["PAY_FROM_ACCOUNT"]=="Y")
	{
		?>
		<tr>
			<td colspan="2" class="account">
				<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">

				<label for="PAY_CURRENT_ACCOUNT">
					<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?> onChange="submitForm()">

					<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" alt="" <?=($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")?"class=\"active\"":"";?> />
					<div class="desc">
						<div class="name"><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?></div>
						<div class="desc">
							<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")." <b>".$arResult["CURRENT_BUDGET_FORMATED"]?></b></div>
							<? if ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"): ?>
								<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT3")?></div>
							<? else: ?>
								<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?></div>
							<? endif; ?>
						</div>
					</div>
				</label>
			</td>
		</tr>
		<?
	}

	// first - those which have descriptions
	foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
	{
		if (strlen($arPaySystem["DESCRIPTION"]) > 0)
		{
			if (count($arResult["PAY_SYSTEM"]) == 1)
			{
				?>
				<tr>
					<td colspan="2" class="account">
						<div class="ps_logo selected">
							<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
							<?if (count($arPaySystem["PSA_LOGOTIP"]) > 0):?>
								<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" title="<?=$arPaySystem["PSA_NAME"];?>"/>
							<?else:?>
								<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" title="<?=$arPaySystem["PSA_NAME"];?>"/>
							<?endif;?>
							<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
								<div class="paysystem_name"><?=$arPaySystem["NAME"];?></div>
							<?endif;?>
						</div>
					</td>
				</tr>
				<?
			}
			else
			{
			?>
				<tr>
				<td colspan="2" class="account">
					<div class="ps_logo with_description">
						<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm();" />

						<label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>">
						<?if (count($arPaySystem["PSA_LOGOTIP"]) > 0):?>
							<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" title="<?=$arPaySystem["PSA_NAME"];?>"/>
						<?else:?>
							<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" title="<?=$arPaySystem["PSA_NAME"];?>" />
						<?endif;?>
							<div class="desc">
								<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
									<div class="name"><?=$arPaySystem["PSA_NAME"];?></div>
								<?endif;?>
								<div class="desc">
									<?=$arPaySystem["DESCRIPTION"]?>
								</div>
							</div>
						</label>
					</div>
					</td>
				</tr>
			<?
		}
		}
	}
	?>
	<tr>
		<td>
		<?
		// payment system without descriptions
		foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
		{
			if (strlen($arPaySystem["DESCRIPTION"]) == 0)
			{
				if (count($arResult["PAY_SYSTEM"]) == 1)
				{
					?>
					<div class="ps_logo selected">
						<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
						<?if (count($arPaySystem["PSA_LOGOTIP"]) > 0):?>
							<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" title="<?=$arPaySystem["PSA_NAME"];?>"/>
						<?else:?>
							<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" title="<?=$arPaySystem["PSA_NAME"];?>"/>
						<?endif;?>
						<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
							<div class="paysystem_name">
								<?=$arPaySystem["PSA_NAME"];?>
							</div>
						<?endif;?>
					</div>
					<?
				}
				else
				{
				?>
					<div class="ps_logo">
						<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm();" />

						<label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>">
						<?if (count($arPaySystem["PSA_LOGOTIP"]) > 0):?>
							<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" title="<?=$arPaySystem["PSA_NAME"];?>"/>
						<?else:?>
							<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" title="<?=$arPaySystem["PSA_NAME"];?>" />
						<?endif;?>
							<div class="paysystem_name">
								<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
									<?=$arPaySystem["PSA_NAME"];?>
								<?else:?>
									<?="&nbsp;"?>
								<?endif;?>
							</div>
						</label>
					</div>
				<?
				}
			}
		}
		?>
	</td>
</tr>
</table>
</div>