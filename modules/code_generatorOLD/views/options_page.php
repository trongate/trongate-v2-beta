<div id="codegen-select-container" class="mt-3">
        <p>Please Choose An Option</p>
        <div id="select-box" onclick="revealOptions()">Select Option...</div>
</div>

    <ul class="options-list" style="display: none;">
        <?php
        foreach($options as $option) {
            echo '<li class="option" mx-get="'.$option['target_url'].'" mx-target="#center-stage" mx-after-swap="formOnInput">'.$option['value'].'</li>';
        }
        ?>
    </ul>

<?php
/*

<div id="centroid" class="mt-3" style="display: none">
    <ul id="possible_options_mini">
        <li id="option" mx-get="<?= BASE_URL ?>desktop_app_mx/enter_mod_name" 
                                   mx-select="#stage"
                                   mx-target="#codegen-stage" 
                                   mx-after-swap="codeGenFocusOnInput()">New Trongate Module</li>
        <li id="option" onclick="generator.quitCodeGen()">Browse The Module Market</li>
    </ul>
</div>
*/
?>