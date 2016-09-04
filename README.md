# ReferralCandy PHP API Client
ReferralCandy PHP API Client by <a href="https://likebtn.com" title="Like Button For Website">LikeBtn.com</a>

## Installation

    require_once("ReferralCandy.php");

## Usage

### Signup Method

Sign an advocate up at ReferralCandy and retrieve the advocate's Referral Link code and Portal Sharing Page code.

    $params = array(
        'first_name' => 'Mike',
        'last_name' => 'Button,
        'email' => 'mikebutton@likebtn.com'
    );

    $rc = new ReferralCandy('access_id', 'secret_key');
    $result = $rc->request('signup', $params);

    if ($result['success'] && ($result['response']['message'] == Referralcandy::MESSAGE_SUCCESS || $result['response']['message'] == 'Contact already signed up.')) {
    	// Advocate has been successfully registered at ReferralCandy
        $link_code = preg_replace("/.*\/([^\/]+)/", '$1', $result['response']['referral_link']);
        $portal_code = preg_replace("/.*\/([^\/]+)/", '$1', $result['response']['referralcorner_url']);
    }

### Purchase Method

Register a new purchase at ReferralCandy.

    $params = array(
        'first_name' => 'Mike',
        'last_name' => 'Button,
        'email' => 'mikebutton@likebtn.com',
        'locale' => 'en',
        'accepts_marketing' => 'false',
        'order_timestamp' => time(),
        'browser_ip' => $_SERVER["REMOTE_ADDR"] ,
        'user_agent' => $_SERVER ['HTTP_USER_AGENT'],
        'invoice_amount' => 7.99,
        'currency_code' => 'USD',
        'external_reference_id' => 123
    );
	
	$rc = new ReferralCandy('access_id', 'secret_key');
    $result = $rc->request('purchase', $params);

	if ($result['success'] && ($result['response']['message'] == Referralcandy::MESSAGE_SUCCESS) {
		// Purchase has been successfully registered at ReferralCandy
    }

### Unsubscribed Method

Unsubscribe a contact at ReferralCandy.

    $params = array(
        'email' => 'mikebutton@likebtn.com',
        'unsubscribed' => 'true'
    );

    $rc = new ReferralCandy('access_id', 'secret_key');
    $rc->request('unsubscribed', $params);

## Documentation

[ReferralCandy API Documentation](http://www.referralcandy.com/api)