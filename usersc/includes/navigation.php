<?php
?>

<div class="collapse navbar-collapse navbar-top-menu-collapse navbar-left"> <!-- Left navigation items -->
	<ul class="nav navbar-nav ">

        <?php if ( ($user->roles() && in_array("Uploader", $user->roles()))
                ||
            ($user->roles() && in_array("Administrator", $user->roles()))
            ){ ?>
            <li><a href="<?=$us_url_root?>pages/upload.php"><i class="fa fa-fw fa-upload"></i> Upload </a></li> <!-- Common for Hamburger and Regular menus link -->
            <li><a href="<?=$us_url_root?>pages/validate.php"><i class="fa fa-fw fa-check"></i> Validate </a></li>
            <li><a href="<?=$us_url_root?>pages/upload_history.php"><i class="fa fa-fw fa-history"></i> History </a></li>
        <?php } ?>



        <?php if ($user->roles() && in_array("Administrator", $user->roles())) { ?>
            <li><a href="<?=$us_url_root?>pages/mappings.php"><i class="fa fa-fw fa-exchange"></i> Mappings </a></li> <!-- Common for Hamburger and Regular menus link -->
            <!-- Common for Hamburger and Regular menus link -->
            <li><a href="<?=$us_url_root?>pages/status.php"><i class="fa fa-fw fa-dashboard"></i> Status </a></li> <!-- Common for Hamburger and Regular menus link -->
        <?php } ?>

<!-- Custom menus. Uncomment or copy/paste to use
		<li class="dropdown"><a class="dropdown-toggle" href="" data-toggle="dropdown"><i class="fa fa-wrench"></i> Custom 1 <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="<?=$us_url_root?>"><i class="fa fa-wrench"></i> Item 1</a></li>
				<li><a href="<?=$us_url_root?>"><i class="fa fa-wrench"></i> Item 2</a></li>
				<li><a href="<?=$us_url_root?>"><i class="fa fa-wrench"></i> Item 3</a></li>
			</ul>
		</li>
		
		<li class="dropdown"><a class="dropdown-toggle" href="" data-toggle="dropdown"><i class="fa fa-wrench"></i> Custom 2 <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="<?=$us_url_root?>"><i class="fa fa-wrench"></i> Item 1</a></li>
				<li><a href="<?=$us_url_root?>"><i class="fa fa-wrench"></i> Item 2</a></li>
				<li><a href="<?=$us_url_root?>"><i class="fa fa-wrench"></i> Item 3</a></li>
			</ul>
		</li>
		
		<li><a href="/"><i class="fa fa-home"></i> Other</a></li>
                              -->
	</ul>
</div>	 <!-- End left navigation items -->	

<?php
?>