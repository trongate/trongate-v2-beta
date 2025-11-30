<?php
if (isset($next_event_title)) { ?>

<div class="alert alert-info" role="alert" id="online">
  <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
  NEXT ONLINE EVENT: <?php echo $next_event_title; ?> | <?php
  echo $next_event_date; ?>&nbsp; <a href="<?php
  echo $next_event_url;
  ?>" class="learn">Learn More</a>
  <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
</div><?php
}
?>

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
  <div class="container">
    <h1 class="french_glasgow">If you want to learn or practice French <i>and</i> make new friends from anywhere in the world, then you should check out French Ignition.</h1>
    <p>At French Ignition, we believe that the best way to learn how to speak French is to speak it. We now host online events every week, making it easy for you to join from wherever you are.
       Join us and you'll enjoy; virtual conversation groups, online cinema trips, book clubs, dining experiences, concerts, special events, and even virtual trips to France!
       All ages are welcome, complete beginners are welcome, and first timers can come along for free.
    </p>
    <p><a class="btn btn-primary btn-lg" href="<?php echo BASE_URL; ?>upcomingevents" role="button"> View Upcoming Online Events &raquo;</a>
       <a class="btn btn-danger btn-lg" href="<?php echo BASE_URL; ?>members_public/start" role="button">Join French Ignition &raquo;</a>
    </p>
  </div>
</div>

<div class="col-md-9">
  <div id="courses"><strong>French courses and classes online</strong></div>
    <h2>From virtual conversation groups to online social events, you get everything you need to practise your spoken French!</h2>
    <p>Have you ever felt that no matter how much French you know, you find yourself tongue-tied when it comes to actually speaking it? Don't worry, you're not alone. All you need is increased confidence and French Ignition is here to help you gain it effortlessly, in a fun and relaxed environment.
  Join our friendly online French conversation groups for a French chat. Even if you're a complete beginner, you're welcome to come along. <b>Non member? No problem! Non members can come along to their first conversation group for free!</b></p>

  <p align="right">
    <a class="btn btn-primary btn-lg" href="<?php echo BASE_URL; ?>ourcalendar" role="button">RSVP For An Online Event Now &raquo;</a>
    </p>

  <div class="row">
    <div class="col-md-4" id="french">
      <h2>We've taken the classroom out of the conversation</h2>
              <p style="min-height: 190px;">When you join French Ignition, you'll be able to choose from a wide assortment of opportunities to speak French and enjoy French culture online.
                Not only will you be able to improve your spoken French, but you'll also be exposed to the very best that the French have to offer in terms of exquisite food, fine wines, music, cinema, and literature.</p>
              <p><a class="btn btn-danger" href="<?php echo BASE_URL; ?>french-conversation-classes-online" role="button">Learn More &raquo;</a></p>
    </div>

    <div class="col-md-4" id="french">
      <h2>Beginners and non-beginners are all welcome</h2>
              <p style="min-height: 190px;">You don’t need to be fluent in French to have a great time at French Ignition.
                Even if you are a complete beginner, French Ignition has a group tailored to your needs. You will get
                plenty of support from our native speakers and more fluent group members are ever so happy to help.</p>
              <p><a class="btn btn-danger" href="<?php echo BASE_URL; ?>learn-french-online" role="button">Learn More &raquo;</a></p>
    </div>

    <div class="col-md-4" id="french">
      <h2>We hold online events every week</h2>
              <p style="min-height: 190px;">Do you love to read? Why not join our virtual cafe litteraire?

Why not meet with us to enjoy French films at our new Online Cinema Club? This group is free and open to everyone.

We also offer a wide range of exciting online social events. Simply choose your events and join us. It's a great way to meet new people!</p>
              <p><a class="btn btn-danger" href="<?php echo BASE_URL; ?>french-meetup-online" role="button">Learn More &raquo;</a></p>
    </div>
  </div>
</div>

<div class="col-md-3">
   <div class="list-group">
     <a href="#" class="list-group-item active">Upcoming Online Events</a>
     <?php
     // $this->module('calendar_events');
     // $this->module('timestamp');
     // $queryb = $this->calendar_events->get('date_as_timestamp', 11);

     $queryb = Modules::run('calendar_events/get', 'date_as_timestamp', 11);

     foreach($queryb as $rowb) {
      $event_title = $rowb->event_title;
      $event_date = $rowb->date_as_timestamp;
      $event_date = date('jS M Y', $event_date);
      $target_url = BASE_URL."ourcalendar/display_event/".$rowb->id;
      echo '<a href="'.$target_url.'" class="list-group-item">'.$event_date.': '.$event_title.'</a>
      ';
     }

     if (!isset($event_title)) {
      echo '<p style="margin-top: 1em;">We hold online events throughout the week. Please <a href="contactus">contact us</a> for more information.</p>';
     }
     ?>
   </div>
</div>

