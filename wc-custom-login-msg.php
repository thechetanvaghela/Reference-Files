<?php
#functions.php
function influance_show_login_error($parm){
    if(isset($_POST['custom-login']) && !empty($_POST['custom-login']))
    {
        add_action ('woocommerce_login_custom_msg', function() use ($parm) {
            echo '<ul class="woocommerce-error custom-error-message" role="alert" style="color:red;">
            <li>'.$parm.'</li>
            </ul>'; 
        }, 10 );
    }
    else
    {
        return $parm;
    }
}
add_filter('login_errors','influance_show_login_error' );


#footer.php
?>
 <div class="login-reg-form">
    <div class="form-wrap">
      <a href="javascript:void(0);" class="btn_close">Close</a>
      <div class="login_form">
        <div class="form-title">Login</div>
        <?php
        echo"<div class='error-msgs'>";
        //woocommerce_output_all_notices();
        do_action( 'woocommerce_login_custom_msg' );
        echo"</div>"; 
        ?>
        <form class="woocommerce-form woocommerce-form-login login" method="post">

          <?php do_action( 'woocommerce_login_form_start' ); ?>

          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
           <!--  <label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" placeholder="Email address or Username*" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required /><?php // @codingStandardsIgnoreLine ?>
          </p>
          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
           <!--  <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label> -->
            <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" placeholder="Password*" id="password" autocomplete="current-password" required />
          </p>

          <?php do_action( 'woocommerce_login_form' ); ?>

          <p class="form-row btn-block">
            <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
            <input type="hidden" name="custom-login" value="1">
            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit btn black-btn custom_model_login" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
            <span class="wpcf7-checkbox">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme ">
              <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
              <span class="wpcf7-list-item-label"><?php esc_html_e( 'Keep me signed in', 'woocommerce' ); ?></span>
            </label>
          </span>
          </p>
          <div class="woocommerce-LostPassword lost_password">
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="forgot-password"><?php esc_html_e( 'Forgot your Password?', 'woocommerce' ); ?></a>
          </div>

          <?php do_action( 'woocommerce_login_form_end' ); ?>

        </form>
      </div>
  </div>
</div>
      <?php