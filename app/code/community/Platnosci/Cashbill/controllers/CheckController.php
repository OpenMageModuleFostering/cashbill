<?php
    $version = '1.2.0';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.cashbill.pl/ajax/module-version-checker.php?cmd=info&module=Magento");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response);

    if(empty($response))
    {

        echo '';
        exit();

    }

    if ($response->version == $version)
    {
        echo '<p style="text-align:center;">Posiadasz najnowszą wersję modułu płatności CashBill dla '.$response->name.'</p>';
    exit();
    }

    echo '<p style="text-align:center;">Aktualnie posiadasz wersję ' . $version . ' jednak jest już dostępna nowsza wersja ' . $response->version . '.</p><p style="text-align:center;"> Możesz ją pobrać korzystając z tego linku <a href="http://www.cashbill.pl/' . $response->link . '">moduł dla ' . $response->name . ' w wersji ' . $response->version . '</a><p>';
