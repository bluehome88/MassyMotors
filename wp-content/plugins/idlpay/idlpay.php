<?php
/**
 * Plugin Name: idlPay.com for WooCommerce
 * Plugin URI: http://interactivedatalabs.com/
 * Description: This plugin adds a payment option in WooCommerce for customers to pay with their Credit Cards.
 * Version: 1.0.0
 * Author: Interactive Data Labs (Trinidad) Limited
 * Developer: Roger J. Kirton 
 * Developer URI: http://interactivedatalabs.com/
 * Text Domain: idlpay
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}


/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here
	add_action( 'plugins_loaded', 'idlpay_init', 11 );
	
}

function idlpay_init() {

    class IDLPAY extends WC_Payment_Gateway {
        
            protected $notify_url;
        
            /**
             * Constructor for the gateway.
             */
        
			public function __construct() {
                
				$this->id                 = 'idlpay';
				$this->has_fields         = false;
				$this->order_button_text  = __( 'Pay with Credit Card (VISA or MASTERCARD)', 'woocommerce' );
				$this->method_title       = __( 'idlPay.com', 'woocommerce' );
				$this->method_description = sprintf( __( 'Accept credit card payments online. ', 'woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-status' ) . '">', '</a>' );
				$this->supports           = array(
					'products',
					'refunds'
				);
                
                $this->notify_url     = WC()->api_request_url('IDLPAY');
		
				// Load the settings.
				$this->init_form_fields();
				$this->init_settings();								
								
				//
				$this->enabled = $this->get_option('enabled');
				$this->instructions = $this->get_option('instructions');
				$this->api_id = $this->get_option('api_key');
				$this->api_pw = $this->get_option('api_secret');
				//$this->testmode = $this->get_option( 'testmode' );
				//$this->test_merchant_id = $this->get_option( 'test_merchant_id' );
				//$this->test_merchant_pw = $this->get_option( 'test_merchant_password' );
				//$this->fac_pageset = $this->get_option( 'fac_pageset' );
				//$this->fac_pagename = $this->get_option( 'fac_pagename' );
				//$this->capture = $this->get_option( 'capture' );
				
				
				if(isset($_GET['order_num'])){
					$this->idlpay_update_order();
					//die("line " . __LINE__);	
				}
				
				
				//hooks
				// Payment listener/API hook
       		   // add_action( 'woocommerce_api_wc_gateway_IDLPAY', array( $this, 'idlpay_update_order' ) );
				add_action('admin_notices', array($this, 'admin_notices'));
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
												
			}
			
			
			
			/**
			 * Notify of issues in wp-admin
			 */
			public function admin_notices()
			{
				if ($this->enabled == 'no'){
					return;
				}
		
				// Check required fields
				if (!$this->api_id){
					echo '<div class="error"><p>' . sprintf( __( 'idlPay.com Error: Please enter your API Key <a href="%s">here</a> ', 'idlpay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=idlpay' ) ) . '</p></div>';
					return;
				}elseif (!$this->api_pw){
					echo '<div class="error"><p>' . sprintf( __( 'idlPay Error: Please enter your api secret <a href="%s">here</a>', 'idlpay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=idl_fac' )) . '</p></div>';
					return;
				}
		
				// Check if enabled and force SSL is disabled
				/*if ( get_option('woocommerce_force_ssl_checkout') == 'no' ) {
					echo '<div class="error"><p>' . sprintf( __( 'idlPay.com is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate - idlPay.com will only work in test mode.', 'idlpay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) . '</p></div>';
					return;
				}*/
			}
			
			/**
			 * Initialize Gateway Settings Form Fields
			 */
			public function init_form_fields() {
				  
				$this->form_fields = array(
					  
					'enabled' => array(
						'title'   => __( 'Enable/Disable', 'idlpay' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable idlPay.com', 'idlpay' ),
						'default' => 'yes'
					),
			
					'title' => array(
						'title'       => __( 'Title', 'idlpay' ),
						'type'        => 'text',
						'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'idlpay' ),
						'default'     => __( 'Pay by Credit Card (VISA or MASTERCARD)', 'idlpay' ),
						'desc_tip'    => true,
					),
                                           'api_key' => array(
                                                            'title'       => __( 'API Key', 'idlpay' ),
                                                            'type'        => 'text',
                                                            'default'     => __( '', 'idlpay' ),
                                                            'desc_tip'    => true,
                                                            ),
                                           'api_secret' => array(
                                                            'title'       => __( 'API Secret', 'idlpay' ),
                                                            'type'        => 'text',
                                                            'default'     => __( '', 'idlpay' ),
                                                            'desc_tip'    => true,
                                                            ),
			
					'description' => array(
						'title'       => __( 'Description', 'idlpay' ),
						'type'        => 'textarea',
						'description' => __( 'Payment method description that the customer will see on your checkout.', 'idlpay' ),
						'default'     => __( 'Thank you for your order.', 'idlpay' ),
						'desc_tip'    => true,
					),
			
					'instructions' => array(
						'title'       => __( 'Instructions', 'idlpay' ),
						'type'        => 'textarea',
						'description' => __( 'Instructions that will be added to the thank you page and emails.', 'idlpay' ),
						'default'     => '',
						'desc_tip'    => true,
					)
					
				) ;
			}
			
			/**
			 * Check if the gateway is available for use
			 *
			 * @return bool
			 */
			public function is_available()
			{
				$is_available = parent::is_available();
		
				// Only allow unencrypted connections when testing
				/*if (!is_ssl() && !$this->testmode)
				{
					$is_available = false;
				}*/
		
				// Required fields check
				if (!$this->api_id || !$this->api_pw)
				{
					$is_available = false;
				}
		
				return $is_available;
			}
			
			
			public function formatTotal($amount){
				return str_pad(''.($amount * 100), 12, "0", STR_PAD_LEFT);
			}


			function process_payment( $order_id ) {
				$order = new WC_Order( $order_id );
				$paymentUrl = $this->_getPaymentPage($order, $order_id);
				
                // Return thankyou redirect
				return array(
					'result'    => 'success',
					'redirect'  => $paymentUrl
				);
			}
			
						
			
			function thankyou() {
				echo $this->instructions != '' ? wpautop( $this->instructions ) : '';
			}
			
			
			public function idlpay_update_order(){
    			
				
				$order_num = rtrim($_GET['order_num'], "/");						
    			$order = wc_get_order( $order_num );				
				$ID = $_GET['ID'];
				$respCode = $_GET['RespCode'];
				$reasonCode = $_GET['ReasonCode'];
								
				parse_str($_SERVER['QUERY_STRING']);
				
				$host = 'marlin.firstatlanticcommerce.com';
				$wsdlurl = 'https://' . $host . '/PGService/HostedPage.svc?wsdl';
				$loclurl = 'https://' . $host . '/PGService/HostedPage.svc';
				$options = array(
								'location' => $loclurl,
								'soap_version'=>SOAP_1_1,
								'exceptions'=>0,
								'trace'=>1,
								'cache_wsdl'=>WSDL_CACHE_NONE
							);
				$client = new SoapClient($wsdlurl, $options);
				$result = $client->HostedPageResults(array('key' => $ID));
				
				$originalResponse = "";
				$originalResponse = $result->HostedPageResultsResult->AuthResponse->CreditCardTransactionResults->OriginalResponseCode;
				
				if ("80" == $originalResponse || "82" == $originalResponse){
    				$order->add_order_note( __( 'Payment Error', 'woocommerce' ) );
    				$order->update_status( 'failed', sprintf( __( 'Payment %s | %s. Original response: %s', 'woocommerce' ), $respCode, $reasonCode, $originalResponse ));
					
					$msg = 'There was an error with your payment. Your credit card was NOT charged. Please review your credit card details and try again.';

					wc_add_notice( __('Payment Error: ', 'woocommerce') . '<br>'.$msg, 'error' );
					wp_redirect('http://ubeedeals.com/checkout/');
					exit;
    		
    			}
								
    	
    			if("1" == $respCode ){
    	
					// add payment details
					update_post_meta( $order->id, 'Trans Signature', wc_clean($ID));				
					$order->update_status( 'processing', sprintf(__( 'Payment complete. Signature: %s'), wc_clean($ID), 'idlpay' ) );
					
					// Reduce stock levels
					$order->reduce_order_stock();
						
					// Remove cart
					WC()->cart->empty_cart();					
					wp_redirect('http://ubeedeals.com/thank-you/');
					exit;
					
    			} else if ("2" == $respCode ){
    				$order->add_order_note( __( 'Payment Error', 'woocommerce' ) );
    				$order->update_status( 'failed', sprintf( __( 'Payment %s | %s. Original Response:', 'woocommerce' ), $respCode, $reasonCode, $originalResponse ));
					
					switch ($_GET['ReasonCode']) {
						case 2:
							$msg = 'Your credit card was DECLINED. Please contact your card-issuing bank and try again.';
							break;
						case 3:
							$msg = 'Your credit card was DECLINED. Please contact your card-issuing bank and try again.';
							break;
						case 4:
							$msg = 'Your credit card was DECLINED and REPORTED. Please contact your card-issuing bank and try again.';
							break;
						case 38:
							$msg = 'Transaction was BLOCKED by your card-issuing bank for security reasons. Please contact your card-issuing bank and try again.';
							break;
						case 39:
							$msg = 'Your transaction TIMED OUT. Please contact your bank and try again later.';
							break;
						default:
							$msg = 'There was an error with your payment. Your credit card was NOT charged. Please contact your bank and try again.';
					}
					
					wc_add_notice( __('Payment Error: ', 'woocommerce') . '<br>'.$msg, 'error' );
					wp_redirect('http://ubeedeals.com/checkout/');
					exit;
    		
    			}else{
					
					$order->add_order_note( __( 'System Error', 'woocommerce' ) );
    				$order->update_status( 'failed', sprintf( __( 'Payment %s | %s.', 'woocommerce' ), $respCode, $reasonCode ));
					
					wc_add_notice( __('System Error: ', 'woocommerce') . '<br>There was an error with the payment system. Your credit card was NOT charged and our support team has been notified. Please try again later.', 'error' );					
					wp_redirect('http://ubeedeals.com/checkout/');
					exit;
				}
   			}

			// Function to get the client IP address
			public function get_client_ip() {
				$ipaddress = '';
				if (getenv('HTTP_CLIENT_IP'))
					$ipaddress = getenv('HTTP_CLIENT_IP');
				else if(getenv('HTTP_X_FORWARDED_FOR'))
					$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
				else if(getenv('HTTP_X_FORWARDED'))
					$ipaddress = getenv('HTTP_X_FORWARDED');
				else if(getenv('HTTP_FORWARDED_FOR'))
					$ipaddress = getenv('HTTP_FORWARDED_FOR');
				else if(getenv('HTTP_FORWARDED'))
				   $ipaddress = getenv('HTTP_FORWARDED');
				else if(getenv('REMOTE_ADDR'))
					$ipaddress = getenv('REMOTE_ADDR');
				else
					$ipaddress = 'UNKNOWN';
				return $ipaddress;
			}
        
        protected function _getPaymentPage($order,$attempt=1){
            $appid=$this->api_id;
            $secret=$this->api_pw;
            $currency='TTD';
            
            // Get IDLPay gateway token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "idlpay.com/api/payment/service/getToken?appid=".$appid."&secret=".$secret);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            
            $output = json_decode($output);
            
            $token = $output->response;

            $params = array("appid"=>$appid,
                            "token"=>$token,
                            "bill_amount"=>$this->get_order_total(),
                            "bill_firstname"=>$order->billing_first_name,
                            "bill_lastname"=>$order->billing_last_name,
                            "bill_email"=>$order->billing_email,
                            "bill_phone"=>$order->billing_phone,
                            "details"=>"Ubee Deals Purchase - Order# ".$order->id,
                            "domain_trxnId"=>$order->id,
                            "notify"=>$this->notify_url,
                            "redirect"=>$this->get_return_url($order),
                            "shipping_amount"=>$order->get_total_shipping(),
                            "currency"=>$currency
                            );
            
            foreach ($params as $h=>&$v){
                $v = trim($v);
            }
            
            $ch = curl_init();
            
            $paytApiUrl = "idlpay.com/api/payment/service/getPaymentPage";
            
            curl_setopt($ch, CURLOPT_URL, $paytApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $output = curl_exec($ch);
            curl_close($ch);
            
            $output = json_decode($output);
            
            if (is_object($output) && @$output->redirect){
                $paymentUrl = $output->redirect->url;
                
                return $paymentUrl;
                //header("Location: $url");
                //die();
            } else {
                if ($attempt > 1){
                    return $output;
                } else {
                    $this->_getPaymentPage($order,2);
                }
            }
            
        }

    } // end \IDLPAY
			
}


function add_idlpay( $methods ) {
	$methods[] = 'IDLPAY';
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_idlpay' );
