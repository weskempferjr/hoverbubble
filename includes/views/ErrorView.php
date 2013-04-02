<?php
class ErrorView {
	public static function displayErrorPage($exception, $statusMessage) {
		?>
		<div class="wrap">
		<h2>Uh oh! Something really bad has happened!</h2>
		<p>Exception info: <?php echo $exception->getMessage();?><p>
		<p>Stacktrace: <?php echo $exception->getTraceAsString();?> </p>
		<p>Additional info:<?php echo  $statusMessage ; ?> </p>	
		</div>
		<?php
	}
}
?>