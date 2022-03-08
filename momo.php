<?php
	 //Random txref generator
	 $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-';
 
	 function generate_string($input, $strength = 16) {
		 $input_length = strlen($input);
		 $random_string = '';
		 for($i = 0; $i < $strength; $i++) {
			 $random_character = $input[mt_rand(0, $input_length - 1)];
			 $random_string .= $random_character;
		 }
	  
		 return $random_string;
	 }


	 // taking input values

	 $name = $_POST['name'];
	 $email = $_POST['email'];
	 $telNo = $_POST['number'];
	 $amount = $_POST['amount'];
	 $description = $_POST['paymentNote'];
	 $transId = generate_string($permitted_chars, 100);
//charging the customer with RAVE
			
			
				$curl = curl_init();
			
			$customer_firstname	= $name;
			$customer_email = $email;
			$customer_phone = $telNo;
			$amount = $amount;  
			$currency = "GHS";
			$txref = $transId; // ensure you generate unique references per transaction.
			$PBFPubKey = "FLWPUBK-d51ec2174bf4ddaa507c34a14a69127b-X"; // get your public key from the dashboard.
			$redirect_url = "https://uvitechgh.com?name=".$customer_email.""; // your call back url
			$payment_plan= "";// this variable must be in the parameters else you won't have mobile money option
			
			
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode([
				'amount'=>$amount,
				'customer_email'=>$email,
				'customer_firstname'=> $name,
				'customer_phone' => $telNo,
				'currency'=>$currency,
				'txref'=>$txref,
				'PBFPubKey'=>$PBFPubKey,
				'redirect_url'=>$redirect_url,
				'payment_plan'=>$payment_plan
			  ]),
			  CURLOPT_HTTPHEADER => [
				"content-type: application/json",
				"cache-control: no-cache"
			  ],
			));
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
			
			if($err){
			  // there was an error contacting the rave API
			  die('Curl returned error: ' . $err);
			}
			
			$transaction = json_decode($response);
			
			if(!$transaction->data && !$transaction->data->link){
			  // there was an error from the API
			  print_r('API returned error: ' . $transaction->message);
			}
			
			// uncomment out this line if you want to redirect the user to the payment page
			//print_r($transaction->data->message);
			
			
			// redirect to page so User can pay
			// uncomment this line to allow the user redirect to the payment page
			header('Location: ' . $transaction->data->link);
								

?>
