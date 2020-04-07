<?php


namespace App\Controller;


// imports
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\LoginRegister;
use App\Entity\Orders;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Database controller to process request to the datebase for 
//logging in and registering new user account

class DatabaseController extends AbstractController
{
	
	/**
	* @Route("/login") methods("GET", "POST");
	*/
	public function Login()
	{
		// store data from webpage request
		$request = Request::createFromGlobals();
		
		//get the variables passed in to the HTML login page
		$username = $request->request->get('username', 'none');
		$password = $request->request->get('password', 'none');
		
		//create a repository object and pass in the LoginRegister entity 
		$repo = $this->getDoctrine()->getRepository(LoginRegister::class);
		
		//checks if there is a match in the database for specified Username
		$person = $repo->findOneBy(['username' => $username]);
		
		
		// checks to make sure a person was found
		//then performs a password_verify()
		//compares the password typed in versus the hashed password stored in the db assocatied to user
        if($person != null && password_verify($password, $person->getPassword()))
		{	
			//pass back control to twig login script tags with account type stored in data 
			return new Response($person->getAcctype());
		}
		else
		{
			//pass back control to twing script and pass back error message to data
			return new Response("Incorrect Login");
		}
		
	}
	
	
	/**
	* @Route("/register") methods("GET", "POST");
	*/
	public function registerUser()
	{
		$request = Request::createFromGlobals();
		
		//get the variables passed in to the HTML Register page
		$username = $request->request->get('username', 'none');
		$password = $request->request->get('password', 'none');
		$acctype = $request->request->get('acctype', 'none');
		
		// insert into DB using Doctrine Entities 
		// create a Doctrine entity mangager
		$entityManager = $this->getDoctrine()->getManager();
		
		
		//create a repository object and pass in the LoginRegister entity 
		$repo = $this->getDoctrine()->getRepository(LoginRegister::class);
		
		//checks if there is a match in the database for specified Username
		$person = $repo->findOneBy(['username' => $username]);
		
		//checks that the user name being passed in is unique
		if ($person ==null)
		{
			//create a $LoginRegister object to use the entities methods
			$LoginRegister = new LoginRegister();
			
			//creates a hashed version of the password user enters
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$LoginRegister->setUsername($username);
			$LoginRegister->setPassword($hashed_password); // store hashed version in db
			$LoginRegister->setAcctype($acctype);
			
			
			//prepare data to be used
			$entityManager->persist($LoginRegister);
			
			
			//execute the SQL to update DB using data from LoginRegister persist
			$entityManager->flush();
			return new Response('registered');
		}
		//pass back control to twig script tags and store message in data
		return new Response('please try a different username');
	}
	
	
	/**
	* @Route("/logOrder") methods("GET", "POST");
	*/
	public function logOrder()
	{
		$request = Request::createFromGlobals();
		
		//get the variables passed in to the HTML Register page
		$user = $request->request->get('username', 'none');
		$cart = $request->request->get('order', 'none');
		$cost = $request->request->get('cost', 'none');
		$addr = $request->request->get('add', 'none');
		$date = date("Y/m/d");
		$status = "waiting";
		
		
		$entityManager = $this->getDoctrine()->getManager();
		
		//create a $Orders object to use the entities methods
		$Orders = new Orders();
		
		$Orders->setOrderDetails($cart);
		$Orders->setCost((float)$cost);
		$Orders->setDeliveryAddress($addr);
		$Orders->setDate(date("Y/m/d"));
		$Orders->setStatus($status);
		$Orders->setUsername("user");
		
		
		//prepare data to be used
		$entityManager->persist($Orders);
		
		
		//execute the SQL to update DB using data from LoginRegister persist
		$entityManager->flush();	
		
		
		return new Response($cart . $cost . $addr . $date . $status . $user );
	}
	
	
	/**
	* @Route("/orderHistory") methods("GET", "POST");
	*/
		public function orderHistory()
	{
		// store data from webpage request
		$request = Request::createFromGlobals();
		
		//get the variables passed in to the HTML login page
		$username = $request->request->get('user', 'none');
		
		//create a repository object and pass in the LoginRegister entity 
		$repo = $this->getDoctrine()->getRepository(Orders::class);
		
		//checks if there is a match in the database for specified Username
		$orders = $repo->findBy(['username' => "user"]);

		return $this->render("orderHistory.html.twig", ['orders' => $orders]);
		
	}
	
}
