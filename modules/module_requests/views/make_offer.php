<section class="force_tall">
    <div class="container container-xs">
        <h1>Make An Offer</h1>

        <?php
        echo form_open($form_location);
        echo form_label('Your Offer Value:');
        echo form_number('offer_value', '', array('placeholder' => 'Enter the amount that you are asking for...'));

        echo form_label('Your Preferred Currency:');
        echo form_input('offer_currency', '', array('placeholder' => 'Enter the currency that you would like to be paid in...'));

        echo form_label('Your Special Terms: <span>optional</span>');
        echo form_textarea('special_terms', '', array('placeholder' => 'Use this space to enter any special terms that you have...', 'rows' => 7));

        echo validation_errors('understood');
        echo '<div>';
        echo form_checkbox('understood', 1, $understood);
        echo ' If I get ripped off then I won\'t blame the makers of Trongate.';
        echo '</div>';

        echo form_submit('submit', 'Submit Offer');
        echo anchor('#', 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>

        <p class="sm"><b>PLEASE NOTE:</b> Any outcome - positive or negative - that occurs as a result of your response is strictly a matter between you and the person(s) who posted the original advert.  The makers of Trongate cannot take any responsibility for the behaviour of people who use our website.</p>
    </div>
</section>