<html>
    <body>
        <table   border="0" cellspacing="0" cellpadding="2">
            <tr><th width="30">&nbsp;</th><th  width="100" >&nbsp;</th><th  width="130" >&nbsp;</th><th  width="50">&nbsp;</th><th width="50">&nbsp;</th><th width="60">&nbsp;</th><th width="80">&nbsp;</th></tr>

            <tr ><td> </td><td  >Поствщик</td><td >{$firmname}</td><td> </td> <td colspan="3" style="font-size:larger;font-weight:bolder; ">Счет входящий</td></tr>
            <tr ><td> </td><td  >ЄДРПОУ </td><td >{$firmcode}</td><td> </td> <td colspan="3" style="font-size:larger;font-weight:bolder; ">№ {$document_number}</td></tr>
            <tr ><td> </td><td  >Р/c</td><td >{$account}</td><td> </td> <td colspan="3" style="font-size:larger;font-weight:bolder; ">от  {$date}</td></tr>
            <tr ><td> </td><td  > </td><td colspan="5">в "{$bank}" </td></tr>
            <tr ><td> </td><td  > </td><td colspan="5">МФО {$mfo}</td></tr>
            <tr ><td> </td><td  >Адрес</td><td  colspan="5">{$address}</td></tr>
            <tr ><td> </td><td  >Получатель</td><td  colspan="5">{$customername}</td></tr>
            <tr ><td> </td><td  >Плательщик</td><td  colspan="5">Тот же</td></tr>

            <tr >
                <td colspan="7" >
                    <br>Основание:{$base}<br>   
                </td>
            </tr>

            <tr style="font-weight: bolder;"><th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="30">№</th><th colspan="2" width="230"  style="border-top:1px #000 solid;border-bottom:1px #000 solid;" >Наименование</th><th  style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Ед.изм</th><th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Кол.</th><th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="60">Цена</th><th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="80">Сумма</th></tr>
                    {foreach $_detail as $item}
                <tr ><td align="right" >{$item.no}<td  colspan="2">{$item.tovar_name}</td><td>{$item.measure}</td><td align="right">{$item.quantity}</td><td align="right">{$item.price}</td><td align="right">{$item.amount}</td></tr>
                    {/foreach}
            <tr style="font-weight: bolder;" ><td style="border-top:1px #000 solid;"  colspan="6"  align="right" >Всего:</td><td style="border-top:1px #000 solid;" align="right" >{$total}</td></tr>
            <tr style="font-weight: bolder;" ><td  colspan="6"  align="right" >НДС:</td><td align="right">{$nds}</td></tr>
            <tr style="font-weight: bolder;" ><td  colspan="6"  align="right" >Всего  с  НДС:</td><td align="right" >{$totalnds}</td></tr>
            <tr  ><td  colspan="7"  valign="middle" ><br><br><span>Всего на сумму:</span> <span style="font-weight: bolder;">{$summa}</span> <br><br><br></td></tr>

            <tr >  <td> </td>
                <td colspan="2"  >
                    К  оплате  до <b>{$paydate}</b>  
                </td>
                <td  colspan="4">
                    Выписал  
                </td>     

            </tr>
        </table>
        <br> <br>
    </body>
</html>