<section class="force_tall">
  <div class="container">
    <h1>Module Request</h1>
    <div class="info-row container container-sm">
      <table class="top_margin">
        <thead>
          <tr>
            <th colspan="2">
              <div class="break_both_ways">
                <div>Request Details</div>
                <div><a href="http://localhost/trongate_live5/members/update" class="button alt"><i class="fa fa-pencil"></i></a></div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td width="30%">Date Created</td>
            <td><?= date('l jS F Y', $date_created) ?></td>
          </tr>
          <tr>
            <td>Status</td>
            <td><?= ($open == 1) ? 'open' : 'closed' ?></td>
          </tr>
          <tr>
            <td>Budget</td>
            <td><?= $budget ?></td>
          </tr>
          <tr>
            <td>Requested By</td>
            <td><?= anchor('#', 'Davcon') ?></td>
          </tr>
          <tr>
            <td>Title</td>
            <td><?= $request_title ?></td>
          </tr>
          <tr>
            <td>Description</td>
            <td><?= nl2br($request_details) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="text-center">
      <?php
      echo anchor('module_requests/create_offer', 'Make An Offer', array('class' => 'button'));
      echo anchor('module_requests/create_question', 'Ask A Question', array('class' => 'button alt'));
      ?>
    </p>

      <div class="alert alert-info">
        <p style="margin-top:0"><b><i class="fa fa-warning"></i> IMPORTANT</b></p>
        <p>Any deals that take place as a result of the request on this webpage are strictly a matter between the developer who is being hired and the original request poster.</p>
        <p>Nobody from Team Trongate - including David Connelly - takes commission from any of this.  Neither Team Trongate nor David Connelly accepts any responsibily for handling payments or any policing any deals that result from this webpage page. In short, if you're planning on using this page - either as a request maker or as a developer - then you are on your own.</p>

        <p><b>Please do go forwad with the assumption that you have no guarantees whatsoever from anyone.</b></p>
      </div>

    <hr>
    <h3>Offers, Questions &amp; Answers</h3>
    <div class="request-convo">
      <?php
      $row_data['response_type'] = 'question';
      $row_data['username'] = 'Joe Smith';
      $row_data['date_created'] = time();
      $row_data['comment'] = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum facere molestias eum reprehenderit, repudiandae voluptas fugit vitae quisquam sint, rem vero alias possimus recusandae. Debitis laborum autem pariatur error est.';
      for ($i=0; $i < 12; $i++) { 
         $comments[] = (object) $row_data;
      }


      foreach($comments as $comment) {
        ?>

         <div class="comment-row">
           <p class="sm">Posted by D3mon on Monday 7th November 2022 at 20:46 GMT</p>
           <div><?= nl2br($comment->comment) ?></div>
           <div class="text-right sm">
             <?php
             echo anchor('module_requests/create_offer', '<i class=\'fa fa-pencil\'></i> Compose Response', array('class' => 'button alt'));
             echo anchor('module_requests/create_question', 'Accept This Offer <i class=\'fa fa-thumbs-up\'></i>', array('class' => 'button green-btn'));
             ?>
           </div>
         </div>

        <?php
      }
      ?>
    </div>

    <p class="text-center">
      <?php
      echo anchor('module_requests/create_offer', 'Make An Offer', array('class' => 'button'));
      echo anchor('module_requests/create_question', 'Ask A Question', array('class' => 'button alt'));
      ?>
    </p>

  </div>
</section>

<style>
.request-convo {
  max-width: 100%;
  line-height: 1.6em;
}

.request-convo > div {
  padding: 2px 12px 12px 12px;
  border-radius: 6px;
  margin-bottom: 12px;
}

.request-convo > div:nth-child(even) {
  background-color: #223e56aa;
}

.request-convo > div:nth-child(odd) {
  background-color: #515b63aa;
}

.comment-row .sm {
  text-transform: uppercase;
  font-weight: bold;
}
</style>