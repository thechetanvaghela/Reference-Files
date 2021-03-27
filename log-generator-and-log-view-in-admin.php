<?php
# class 
class LogOptionsSettings 
{
	# construct
	function __construct() 
	{
		# add admin menu
		add_action( 'admin_menu', array($this, 'wp_custom_log_admin_menu_callback' ) );
	}
	
	# callback admin menu
	function wp_custom_log_admin_menu_callback() {
		# add admin menu page
		add_menu_page('Logs','Logs','manage_options','crm_logs_files_data',array($this, 'crm_logs_files_data_callback' ),'dashicons-email-alt2');
	}
		
	# admin menu page callback
	function crm_logs_files_data_callback() 
	{

		# define varibles
		$logfiles = array(); 
		$output = array(); 

		# log directory path
        $log_dir = ABSPATH . 'wp-content/uploads/logs/';

        # check log dir exists
		if (file_exists($log_dir)) 
		{
			# get all log files and folders from all log folder
            $logfilesdata = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($log_dir));
            # get all files list
			foreach ($logfilesdata as $file) 
			{
				# check if directory then continue
			    if ($file->isDir())
			    { 
			        continue;
			    }

			    # create array data of filename and file time
			    //$logfiles[] = $file->getPathname(); 
			    $logfiles[$file->getMTime()] =  $file->getRealPath();
			}

			# check file list not empty 	
			if(!empty($logfiles))
			{
				# sort file list by latest
				krsort($logfiles);
			}
		}
			
		# get requested file data if requested
		if ( ! empty( $_REQUEST['log_file'] ) && isset( $logfiles[ sanitize_title( wp_unslash( $_REQUEST['log_file'] ) ) ] ) ) 
		{
			# view log file 
			$viewed_log = $logfiles[ sanitize_title( wp_unslash( $_REQUEST['log_file'] ) ) ];
		} elseif ( ! empty( $logfiles ) ) {
			# display default first file
			$viewed_log = current( $logfiles );
		}
		?>
		<!-- css for view log -->
		<style type="text/css">
			#crm-log-viewer {
			    background: #fff;
			    border: 1px solid #e5e5e5;
			    box-shadow: 0 1px 1px rgb(0 0 0 / 4%);
			    padding: 5px 20px;
			}
			#log-viewer pre {
			    font-family: monospace;
			    white-space: pre-wrap;
			    word-wrap: break-word;
			}
		</style>

		<?php 
		if ( $logfiles ) 
		{
			?>
			<div id="crm-log-viewer-select">
				<h2>Logs</h2>
				<div class="alignleft">
					<h2>
						<?php
							# get filename of viewing 
							echo esc_html( basename($viewed_log )); 
						?>
					</h2>
				</div>
				<div class="alignright">
					<form action="<?php echo esc_url( admin_url( 'admin.php?page=crm_logs_files_data' ) ); ?>" method="post">
						<select name="log_file">
							<?php foreach ( $logfiles as $log_key => $log_file ) : ?>
								<?php
									$timestamp = filemtime($log_file );
									$filename = basename($log_file);
									$date = sprintf( __( '%1$s at %2$s', 'crm' ), date_i18n( get_option( 'date_format' ), $timestamp ), date_i18n( get_option( 'time_format' ), $timestamp ) );
								?>
								<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( sanitize_title(  $_REQUEST['log_file'] ), $log_key ); ?>><?php echo esc_html( $filename ); ?> (<?php echo esc_html( $date ); ?>)</option>
							<?php endforeach; ?>
						</select>
						<button type="submit" class="button" value="<?php esc_attr_e( 'View', 'crm' ); ?>"><?php esc_html_e( 'View', 'crm' ); ?></button>
					</form>
				</div>
				<div class="clear"></div>
			</div>
			<div id="crm-log-viewer">
				<pre><?php echo esc_html( file_get_contents( $viewed_log ) ); ?></pre>
			</div>
		<?php else : ?>
			<div class="updated crm-message inline"><p><?php esc_html_e( 'There are currently no logs to view.', 'crm' ); ?></p></div>
			<?php 
		} 
	}	
}
new LogOptionsSettings;

# log generator function
function log_file_generator($logfilefor,$logStartTime,$logText,$logfilename)
{
	# log file content start
	$logStartTime = date('Y-m-d h:i:s',time());
	$endTime = date('Y-m-d h:i:s',time());
	$log_dir = ABSPATH . 'wp-content/uploads/logs/';
	$currentYear = date('Y', strtotime($endTime));
	$currentMonth = date('m', strtotime($endTime));     
	$currentDay = date('d', strtotime($endTime));
	$logPath = $log_dir.''.$currentYear.'/'.$currentMonth.'/'.$currentDay;

	if (!file_exists($logPath)) {
	    mkdir($logPath, 0777, true);
	}
	
	if(!empty($logText)) {
        $logEndTime = date('Y-m-d h:i:s',time());
        $logHead = "======================================================= \n";
        $logHead .= "LOG File ".$logfilefor." content : Started at: ".$logStartTime."\n";
        $logHead .= "======================================================= \n\n";
        $logFooter = "\n\n======================================================= \n";
        $logFooter .= " Ended at: ".$logEndTime."\n";
        $logFooter .= "======================================================= \n";
        $logText = $logHead.$logText.$logFooter;
        $logFile = fopen($logPath.'/'.$logfilename.'.log', 'w');
        fwrite($logFile, $logText);
        fclose($logFile);
    } 
}