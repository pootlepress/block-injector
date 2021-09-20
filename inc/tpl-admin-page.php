<div class="wrap pmab-wrap">
	<style>
		body {
			background-color: #fff9e9;
		}

		.pmab-wrap {
			margin: 1em auto;
			max-width: 650px;
		}

		.pmab-wrap h1 {
			margin-top: 2rem;
			text-align: center;
			font-size: 72px;
			font-weight: 800;
			letter-spacing: 4px;
		}

		h1 small {
			display: block;
			font-size: 20px;
			letter-spacing: 5px;
			font-weight: 800;
		}

		.pmab-wrap h3 {
			max-width: 25em;
			margin: auto;
			font-weight: 300;
			text-align: center;
			line-height: 1.4;
		}

		.pmab-step {
			display: flex;
			align-items: center;
		}

		.pmab-step .step-x {
			font: 800 70px serif;
			margin: 1.6rem;
		}

		.pmab-step .desc {
			margin-right: 1.6rem;
			padding: 7em 0;
			position: relative;
		}

		.pmab-step h4 {
			font: 30px 'Nothing You Could Do', cursive;
			margin: .5em auto;
		}

		.pmab-step h4:after {
			position: absolute;
			bottom: 2rem;
			left: 0;
			content: '_';
			font-size: 200px;
			line-height: 0;
			transform: scaleY(.25);
		}

		.pmab-step img {
			max-width: 43%;
		}

		.pmab-step-set-location img {
			max-width: 32%;
			margin: 0 7% 0 4%;
		}

		.pmab-step-create-content img {
			max-width: 52%;
			margin: 0 -1rem 0 -2rem;
		}

		.pmab-step:nth-child( 2n + 1 ) img {
			order: 5;
		}
	</style>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Nothing+You+Could+Do&display=swap" rel="stylesheet">

	<h1>
		<small>
			<?php _e( 'HOW TO USE', 'textdomain' ); ?>
		</small>
		Block Injector
	</h1>

	<h3>Block Injector lets you dynamically display Gutenberg Blocks anywhere and anyplace on your website.</h3>

	<?php

	$steps = [
		'create-content' => [
			'img' => '1-create-content.png',
			'title' => 'Create content',
			'desc' => 'Use the WordPress Block Editor to create your layout and design',
		],
		'set-location' => [
			'img' => '2-set-location.png',
			'title' => 'Set location',
			'desc' => 'Choose what location you would like your content to appear.',
		],
		'set-position' => [
			'img' => '3-set-position.png',
			'title' => 'Set position',
			'desc' => 'Set the position of where you would like your content to appear',
		],
		'set-exceptions' => [
			'img' => '4-set-exceptions.png',
			'title' => 'Set exceptions',
			'desc' => 'Choose any exceptions (optional)',
		],
		'schedule' => [
			'img' => '5-schedule.png',
			'title' => 'Schedule',
			'desc' => 'Optionally set a start time and end time.',
		],
	];

	?>

	<div class="pmab-steps">
		<?php
		$i = 0;
		foreach ( $steps as $key => $step ) {
			$i++;
			?>
			<div class="pmab-step pmab-step-<?php echo $key ?>">
				<img src="<?php echo PMAB_Plugin::instance()->asset_url( "assets/$step[img]" ) ?>" alt="">
				<div class="step-x">0<?php echo $i ?></div>
				<div class="desc">
					<h4><?php echo $step['title'] ?></h4>
					<p><?php echo $step['desc'] ?></p>
				</div>
			</div>
			<?php
		}
		?>
	</div>

		<img src="<?php echo PMAB_Plugin::instance()->asset_url( '2-set-location.png' ) ?>" alt="">
		<img src="<?php echo PMAB_Plugin::instance()->asset_url( '3-set-position.png' ) ?>" alt="">
		<img src="<?php echo PMAB_Plugin::instance()->asset_url( '4-set-exceptions.png' ) ?>" alt="">
		<img src="<?php echo PMAB_Plugin::instance()->asset_url( '5-schedule.png' ) ?>" alt="">


</div>