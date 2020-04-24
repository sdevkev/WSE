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
			return new Response('successfully registered. Click OK to go to login screen'); //aka successfully registered
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
		$Orders->setUsername($user);
		
		
		//prepare data to be used
		$entityManager->persist($Orders);
		
		
		//execute the SQL to update DB using data from LoginRegister persist
		$entityManager->flush();	
		
		
		return new Response('');
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
		
		//checks if there is a match in the database for specified Username and stores all the results in $orders as an array
		$orders = $repo->findBy(['username' => $username]);

		// render twig file containing the order history table template and pass in the orders array
		return $this->render("orderHistory.html.twig", ['orders' => $orders]);
		
	}
	
		/**
	* @Route("/activeDeliveries") methods("GET", "POST");
	*/
		public function activeDeliveries()
	{
		
		
		//create a repository object and pass in the LoginRegister entity 
		$repo = $this->getDoctrine()->getRepository(Orders::class);
		
		//checks if there is a match in the database for specified Username
		$orders = $repo->findBy(['status' => "waiting"]);

		return $this->render("awaitingDelivery.html.twig", ['orders' => $orders]);
		
	}
	
	
		/**
	* @Route("/completeOrder") methods("GET", "POST");
	*/
	public function completeOrder()
	{
		$request = Request::createFromGlobals();
		
		//get the variables passed in to the HTML Register page
		$orderid = $request->request->get('completeorder', 'none');

		
		// insert into DB using Doctrine Entities 
		// create a Doctrine entity mangager
		$entityManager = $this->getDoctrine()->getManager();
		
		
		//create a repository object and pass in the LoginRegister entity 
		$repo = $this->getDoctrine()->getRepository(Orders::class);
		
		//checks if there is a match in the database for specified order id
		$order = $repo->findOneBy(['id' => $orderid]);
		
		// set the status field to complete 
		$order->setStatus("complete");
		
		//prepare data to be used
		$entityManager->persist($order);
		
		
		//execute the SQL
		$entityManager->flush();	
		
	   return new Response("order completed" );
		
	}
	
		/**
	* @Route("/generateReport") methods("GET", "POST");
	*/
		public function generateReport()
	{
		// store data from webpage request
		$request = Request::createFromGlobals();
		
		//get the variables passed in to the HTML login page
		$d1 = $request->request->get('date1', 'none');
		$d2 = $request->request->get('date2', 'none');
		
		$repo = $this->getDoctrine()->getRepository(Orders::class);
		
		// custom SQL function that was added to src/repository/OrdersRepository
		$orders = $repo->findAllBetweenDates($d1, $d2);
		
		

		// render twig file containing the order history table template and pass in the orders array
		//return $this->render("orderHistory.html.twig", ['orders' => $orders]);
		
		return $this->render("reportTable.html.twig", ['orders' => $orders]);
		
	}
	
	
			/**
	* @Route("/showOrders") methods("GET", "POST");
	*/
	public function showAllOrders()
	{
		//create a repository object and pass in the LoginRegister entity 
		$repo = $this->getDoctrine()->getRepository(Orders::class);
		
		//
		$orders = $repo->findAll();
		
		//return var_dump($orders);

		return $this->render("showOrdersTable.html.twig", ['orders' => $orders]);
		
		
	}
	
			/**
	* @Route("/cancelOrder") methods("GET", "POST");
	*/
	public function cancelOrder()
	{
		$request = Request::createFromGlobals();
		
		//get the variables passed in to the HTML Register page
		$orderid = $request->request->get('cancelorder', 'none');

		
		// insert into DB using Doctrine Entities 
		// create a Doctrine entity mangager
		$entityManager = $this->getDoctrine()->getManager();
		
		
		//create a repository object and pass in the LoginRegister entity 
		$repo = $this->getDoctrine()->getRepository(Orders::class);
		
		//checks if there is a match in the database for specified order id
		$order = $repo->findOneBy(['id' => $orderid]);
		
		// set the status field to complete 
		$order->setStatus("cancelled");
		
		//prepare data to be used
		$entityManager->persist($order);
		
		
		//execute the SQL
		$entityManager->flush();	
		
	   return new Response("order completed" );
		
	}
	
}
