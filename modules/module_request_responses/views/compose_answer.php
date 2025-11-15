<section class="force_tall">
    <div class="container container-xs">
        <h1>Compose Answer</h1>

        <?php
        echo form_open($form_location, array('class' => 'highlight-errors'));

        echo form_label('Your Answer:');
        echo validation_errors('comment');
        echo form_textarea('comment', $comment, array('placeholder' => 'Use this space to enter your answer...', 'rows' => 7));

        echo validation_errors('understood');
        echo '<div>';
        echo form_checkbox('understood', 1, $understood);
        echo ' If I get ripped off then I won\'t blame the makers of Trongate.';
        echo '</div>';

        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>

        <p class="sm"><b>PLEASE NOTE:</b> Any outcome - positive or negative - that occurs as a result of your response is strictly a matter between you and the persons that you choose to deal with.  The makers of Trongate cannot take any responsibility for the behaviour of people who use our website.</p>
    </div>
</section>