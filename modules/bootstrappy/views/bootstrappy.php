<!DOCTYPE html>
<html lang="en">
<head>
	<base href="<?= BASE_URL ?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/trongate.css">
    <link rel="stylesheet" href="css/admin-slide-nav.css">
    <link rel="stylesheet" href="bootstrappy_module/css/bootstrappy.css">
    <?= $additional_includes_top ?>
	<title>Bootstrappy</title>
</head>
<body>
<div class="top-gutter">
	<div class="logo"><?= anchor('#', OUR_NAME) ?></div>
	<div class="top-rhs">
		<?= Modules::run('messages/_draw_admin_top_rhs', $data) ?>
		<?= Modules::run('module_requests/_draw_admin_top_rhs') ?>
		<?= Modules::run('module_request_responses/_draw_admin_top_rhs') ?>
		<div class="hide-sm language-selector"><?php
		$language_options['ar'] = 'Arabic';
		$language_options['en'] = 'English';
		$language_options['fr'] = 'French';
		echo form_dropdown('language', $language_options, 'en');
		?></div>
		<div id="top-rhs-selector">
		<i class="fa fa-user"></i><span id="admin-down-arrow">▼</span></div>
		<div id="admin-settings-dropdown">
			<ul>
				<li><?= anchor('trongate_administrators/create/1', '<i class=\'fa fa-shield\'></i> Update Your Details ') ?></li>
				<li><?= anchor('trongate_administrators/manage', '<i class=\'fa fa-users\'></i> Manage Admin Users ') ?></li>
				<li class="top-border"><?= anchor('trongate_administrators/logout', '<i class=\'fa fa-sign-out\'></i> Logout ') ?></li>
			</ul>
		</div>
		<div id="hamburger" class="hide-lg" onclick="openSlideNav()">&#9776;</div>
	</div>
</div>
<div class="wrapper" style="opacity:0">
	<div id="sidebar">
		<nav id="left-nav">
			<ul>
				<li><?= anchor('dashboard', '<i class=\'fa fa-tachometer\'></i> Dashboard') ?></li>
				<li class="dropdown"><div><i class="fa fa-envelope"></i> Enquiries</div><div><i class="fa fa-caret-right"></i></div></li>
				<li class="dropdown-area">
					<ul>
				  		<li><?= anchor('#', 'Inbox') ?></li>
				  		<li><?= anchor('#', 'Junk') ?></li>
				  		<li><?= anchor('#', 'Archives') ?></li>
			  	    </ul>				
				</li>
				<li><?= anchor('news/manage', '<i class=\'fa fa-newspaper-o\'></i> Manage News') ?></li>
				<li><?= anchor('module_market_items/manage', '<i class=\'fa fa-cube\'></i> Module Market Items') ?></li>
				<li><?= anchor('module_requests/manage', '<i class=\'fa fa-cubes\'></i> Module Requests') ?></li>
				<li><?= anchor('module_request_responses/manage', '<i class=\'fa fa-comments-o\'></i> Request Responses') ?></li>
				<li><?= anchor('members/manage', '<i class=\'fa fa-users\'></i> Members') ?></li>
		    </ul>
	    </nav>
	</div>
	<div class="center-stage"><?= display($data) ?></div>
</div>
<div class="footer">
	<?= anchor('https://trongate.io/', 'Powered by Trongate') ?>
</div>

    <div id="slide-nav">
        <div id="close-btn" onclick="closeSlideNav()">&times;</div>
        <ul auto-populate="true"></ul>
    </div>

<script src="bootstrappy_module/js/admin.js"></script>
<script src="bootstrappy_module/js/bootstrappy.js"></script>
</body>
</html>