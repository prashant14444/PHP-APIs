<?php
	session_start();
	require ( "db.php" );  
	$Link_L = mysqli_connect($servername, $username_db, $password_db, $dbname);
	if (!$Link_L)
	{
		die("Connection failed: " . mysql_connect_error());
	}
//include("JSON.php");
//$json = new Services_JSON();
$Param = $_POST;

                         //before using userid_list, we need to replace "," by "','"
                        //This stops SQL Injection in _POST / _GET / _REQUEST variables
                          foreach ($_POST as $key => $value)
                            {
                                $value = preg_replace('/[\'\"]/', '`', $value);
                                //$value = preg_replace('/[;]/', ',', $value);
                                 $_POST[$key] = mysqli_real_escape_string($Link_L,$value);

							}
							    if($_GET)
							    foreach ($_GET as $key => $value)
							    {

							      $value = preg_replace('/[\'\"]/', '`', $value);
							      //$value = preg_replace('/[;]/', ',', $value);
							      $_GET[$key] = mysqli_real_escape_string($Link_L,$value);
							    }  

mysqli_query($Link_L,"SET NAMES 'utf8'");
$Param = $_POST;
$json = file_get_contents('php://input');
//echo $json;    //getting body of the post and reading it's content by file_get_contents
$j = json_decode($json);
//var_dump($j);

$purpose=$j->purpose;
//echo $purpose; 
//require("api.php");

if(empty($purpose))
	{
		echo "$"."purpose not set ";
		die();
	}

switch ($purpose) 
{	
	case 'admin_create_question':
		//var_dump(count($j->option));
		$title=$j->title;
		$type=$j->type;
		$text=$j->text;
		//echo $option=$j->option;
		$i=0;
		$option="";
		if(count($j->options)>0)
		{
			for ($i=0; $i <count($j->options) ; $i++) 
			{ 
				//echo $j->option[$i]."<br>";
				$option=$option.$j->options[$i].",";
			}
			$option=chop($option,",");
		//echo $option;
		
		//echo $option;
		}
		else
		{
			$option="";
		}



		$sql="insert into list_q set title='$title',type='$type',text='$text',options='$option'";
		//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
		
		if ($query_run=mysqli_query($Link_L, $sql))
		{
			$response['purpose']="ok";
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
		break;
			//admin is creating list of issues

	case 'admin_create_issue':
		//var_dump(count($j->option));
		$type=$j->type;
		//$i=0;
		$count=0;
		$text="";
		//echo $ct=count($j->text);
		if(count($j->text)>0)
		{
			for ($i=0; $i <count($j->text) ; $i++) 
			{ 
				//echo $j->option[$i]."<br>";
				$text=$j->text[$i];
				//$text=$text.$j->text[$i].",";
				
				//echo $option;
				//$text=chop($text,",");
				//echo $option;
				$sql="insert into list_i set type='$type',text='$text'";
				//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
				if ($query_run=mysqli_query($Link_L, $sql))
				{
					$count=1;
				}
				else
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					break;
				}
			}
		}
		else
			{
				$sql="insert into list_i set type='$type',text=''";
				if ($query_run=mysqli_query($Link_L, $sql))
				{
					$count=1;
				}
				else
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					break;
				}
				
			}
		if ($count==1)
		{
			$response['purpose']="ok";
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		break;

	case 'admin_create_caste':
	//var_dump(count($j->option));
		$type=$j->type;
		$text=$j->text;
		//echo $option=$j->option;
		$count=0;
		$text="";
		if(count($j->text)>0)
		{
			for ($i=0; $i <count($j->text) ; $i++) 
			{ 
				//echo $j->option[$i]."<br>";
				$text=$j->text[$i];
				//$text=$text.$j->text[$i].",";
				
				//echo $option;
				//$text=chop($text,",");
				//echo $option;
				$sql="insert into list_c set type='$type',text='$text'";
				//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
				if ($query_run=mysqli_query($Link_L, $sql))
				{
					$count=1;
				}
				else
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					break;
				}
			}
		}
		else
			{ 
				$sql="insert into list_c set type='$type',text=''";
				if ($query_run=mysqli_query($Link_L, $sql))
				{
					$count=1;
				}
				else
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					break;
				}
			}

		if ($count==1)
		{
			$response['purpose']="ok";
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		break;

	case 'post_login':  // php_Param = "get_count_of_Remaining_Jobs" ;
  		//echo "ok";
  		 $email=$j->email;
		 $password=$j->password;
		 header('Content-Type: application/json');
		$response=array();
		//echo ($email) ;
		$sql = "select * from login where email= '$email' and pw= '$password' ";
		if ($query_run=mysqli_query($Link_L, $sql))
		{ 
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$operator_id_db=($query_row['operator_id']);
				$name_db=($query_row['name']);
				$mobile_db=$query_row['mobile'];
				$email_db=$query_row['email'];
				$status_db=$query_row['status'];
				$type_db=$query_row['type'];
				$st_id_db=$query_row['st_id'];
				$ac_id_db=$query_row['ac_id'];
				$t_q_db=$query_row['t_q'];
				$t_c_db=$query_row['t_c'];
				$t_i_db=$query_row['t_i'];
				$voters_done_db=$query_row['voters_done'];
				$voters_total_db=$query_row['voters_total'];


				$response['operator_id']=$operator_id_db;
				$response['name']=$name_db;
				$response['mobile']=$mobile_db;
				$response['email']=$email_db;
				$response['status']=$status_db;
				$response['type']=$type_db;
				$response['st_id']=$st_id_db;
				$response['ac_id']=$ac_id_db;
				$response['t_q']=$t_q_db;
				$response['t_c']=$t_c_db;
				$response['t_i']=$t_i_db;
				$response['voters_done']=$voters_done_db;
				$response['voters_total']=$voters_total_db;
				$_SESSION['t_q']=$t_q_db;
				$_SESSION['operator_id_login']=$operator_id_db;
				$_SESSION['st_id']=$st_id_db;
				$_SESSION['ac_id']=$ac_id_db;
				$count=1;

			}
			echo json_encode($response);
			//echo ($_SESSION);
			// set the json header content-type
			
			if($count==0)
			{
				//print "Wrong user name or password ";
				session_destroy();
			}
			//echo "session variable value = ".$_SESSION['operator_id_login'];
		}
		else 
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			
	break;

	case 'post_new_operator':
	header('Content-Type: application/json');
		$name=$j->name;
		$mobile=$j->mobile;
		$email=$j->email;
		$password=$j->pw;
		$type=$j->type;
		$st_id=$j->st_id;
		$status=$j->status;
		$ac_id=$j->ac_id;
		$sql="insert into login (name,mobile,email,pw,type,st_id,ac_id,status) values('$name','$mobile','$email','$password','$type','$st_id','$ac_id','$status')";
		if ($query_run=mysqli_query($Link_L, $sql))
		{
			$response['purpose']="ok";
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
	break;

//updating operator information

	case 'operator_info_update':

		header('Content-Type: application/json');
		$operator_id=$j->operator_id;
		$name=$j->name;
		$mobile=$j->mobile;
		$email=$j->email;
		$password=$j->pw;
		$status=$j->status;
		$tq_id=$j->t_q;
		$tc_id=$j->t_c;
		$ti_id=$j->t_i;
		$st_id=$j->st_id;
		$ac_id=$j->ac_id;
		//$sql="select email,mobile from login";
		$sql="update login set name='$name',mobile='$mobile',email='$email',pw='$password',status='$status',st_id='$st_id',ac_id='$ac_id',t_c='$tc_id',t_q='$tq_id',t_i='$ti_id' where operator_id='$operator_id'";
		//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
		
		if ($query_run=mysqli_query($Link_L, $sql))
		{
			$response['purpose']="ok";
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
	break;


	case 'template_allocation_q':
		$insert="";
		$update="";
		$i=0;
		$response_array=array();

		foreach ($j->ta as $item)
			{
				$operator_id=$item->operator_id;
				$template_id=$item->template_id;
				$sql="select * from template_allocation_q where operator_id='$operator_id'";
				{
					if ($query_run=mysqli_query($Link_L, $sql))
					{
						$count_row=mysqli_num_rows($query_run);
						if($count_row==0)
						{
							$insert="insert into template_allocation_q (operator_id,template_id) values ('$operator_id','$template_id')";
							$update1="update login set t_q='$template_id' where operator_id='$operator_id'";
							if ($query_run1=mysqli_query($Link_L, $insert))
							{
								$response["purpose"]="inserted";
							}
							else
							{
								echo "Error: " . $insert . "<br>" . mysqli_error($Link_L);
							}


							if ($query_run1=mysqli_query($Link_L, $update1))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update1 . "<br>" . mysqli_error($Link_L);
							}
						}
						if($count_row==1)
						{
							$update2="update template_allocation_q set operator_id='$operator_id',template_id='$template_id' where operator_id=$operator_id";
							$update3="update login set t_q='$template_id' where operator_id='$operator_id'";


							if ($query_run2=mysqli_query($Link_L, $update2))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update2 . "<br>" . mysqli_error($Link_L);
							}


							if ($query_run3=mysqli_query($Link_L, $update3))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update3 . "<br>" . mysqli_error($Link_L);
							}
						}
					}
					else 
					{
						echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					}
				}
				$i++;
				echo json_encode($response,JSON_PRETTY_PRINT);
				//echo "ok";
			}
		break;
		

	case 'template_allocation_i':
		$insert="";
		$update="";
		$i=0;
		$response_array=array();

		foreach ($j->ta as $item)
			{
				$operator_id=$item->operator_id;
				$template_id=$item->template_id;
				$sql="select * from template_allocation_i where operator_id='$operator_id'";
				{
					if ($query_run=mysqli_query($Link_L, $sql))
					{
						$count_row=mysqli_num_rows($query_run);
						if($count_row==0)
						{
							$insert="insert into template_allocation_i (operator_id,template_id) values ('$operator_id','$template_id')";
							$update1="update login set t_i='$template_id' where operator_id='$operator_id'";
							if ($query_run1=mysqli_query($Link_L, $insert))
							{
								$response["purpose"]="inserted";
							}
							else
							{
								echo "Error: " . $insert . "<br>" . mysqli_error($Link_L);
							}


							if ($query_run1=mysqli_query($Link_L, $update1))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update1 . "<br>" . mysqli_error($Link_L);
							}
						}
						if($count_row==1)
						{
							$update2="update template_allocation_i set operator_id='$operator_id',template_id='$template_id' where operator_id=$operator_id";
							$update3="update login set t_i='$template_id' where operator_id='$operator_id'";


							if ($query_run2=mysqli_query($Link_L, $update2))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update2 . "<br>" . mysqli_error($Link_L);
							}


							if ($query_run3=mysqli_query($Link_L, $update3))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update3 . "<br>" . mysqli_error($Link_L);
							}
						}
					}
					else 
					{
						echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					}
				}
				$i++;
				echo json_encode($response,JSON_PRETTY_PRINT);
				//echo "ok";
			}
		break;

	case 'template_allocation_c':
		$insert="";
		$update="";
		$i=0;
		$response_array=array();

		foreach ($j->ta as $item)
			{
				$operator_id=$item->operator_id;
				$template_id=$item->template_id;
				$sql="select * from template_allocation_c where operator_id='$operator_id'";
				{
					if ($query_run=mysqli_query($Link_L, $sql))
					{
						$count_row=mysqli_num_rows($query_run);
						if($count_row==0)
						{
							$insert="insert into template_allocation_c (operator_id,template_id) values ('$operator_id','$template_id')";
							$update1="update login set t_c='$template_id' where operator_id='$operator_id'";
							if ($query_run1=mysqli_query($Link_L, $insert))
							{
								$response["purpose"]="inserted";
							}
							else
							{
								echo "Error: " . $insert . "<br>" . mysqli_error($Link_L);
							}


							if ($query_run1=mysqli_query($Link_L, $update1))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update1 . "<br>" . mysqli_error($Link_L);
							}
						}
						if($count_row==1)
						{
							$update2="update template_allocation_c set operator_id='$operator_id',template_id='$template_id' where operator_id=$operator_id";
							$update3="update login set t_c='$template_id' where operator_id='$operator_id'";


							if ($query_run2=mysqli_query($Link_L, $update2))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update2 . "<br>" . mysqli_error($Link_L);
							}


							if ($query_run3=mysqli_query($Link_L, $update3))
							{
								$response["purpose"]="updated";
							}
							else
							{
								echo "Error: " . $update3 . "<br>" . mysqli_error($Link_L);
							}
						}
					}
					else 
					{
						echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					}
				}
				$i++;
				echo json_encode($response,JSON_PRETTY_PRINT);
				//echo "ok";
			}
		break;

	case 'create_template_q':
		$template_name=$j->template_name;
		$id=$j->id;
		$i=0;
		$list="";
		for ($i=0; $i <count($j->list) ; $i++) 
		{ 
			//echo $j->option[$i]."<br>";
			$list=$list.$j->list[$i].",";
		}
		//echo $option;
		$list=chop($list,",");
		//echo $option;
		if(empty($id))
		{
			$sql_insert="insert into template_q (qn_list,template_name) values('$list','$template_name')";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_insert))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_insert . "<br>" . mysqli_error($Link_L);
			}
			break;
		}

		$sql="select id from template_q";
		if ($query_run=mysqli_query($Link_L, $sql))
		{
			//var_dump($id);
			$query_row=mysqli_fetch_assoc($query_run);
			$id_db=$query_row['id'];
			if(!empty($id_db))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
		}

		if($flag==0)
		{
			$sql_insert="insert into template_q (qn_list,template_name) values('$list','$template_name')";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_insert))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_insert . "<br>" . mysqli_error($Link_L);
			}
			break;
		}
		if($flag==1)
		{
			$sql_update="update template_q set qn_list='$list',template_name='$template_name' where id='$id'";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_update))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_update . "<br>" . mysqli_error($Link_L);
			}
			break;
		}
		break;


	case 'create_template_i':
		$template_name=$j->template_name;
		$id=$j->id;
		$i=0;
		$list="";
		for ($i=0; $i <count($j->list) ; $i++) 
		{ 
			//echo $j->option[$i]."<br>";
			$list=$list.$j->list[$i].",";
		}
		//echo $option;
		$list=chop($list,",");
		//echo $option;
		if(empty($id))
		{
			$sql_insert="insert into template_i (list,template_name) values('$list','$template_name')";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_insert))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_insert . "<br>" . mysqli_error($Link_L);
			}
			break;
		}

		$sql="select id from template_i";
		if ($query_run=mysqli_query($Link_L, $sql))
		{
			//var_dump($id);
			$query_row=mysqli_fetch_assoc($query_run);
			$id_db=$query_row['id'];
			if(!empty($id_db))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
		}

		if($flag==0)
		{
			$sql_insert="insert into template_i (list,template_name) values('$list','$template_name')";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_insert))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_insert . "<br>" . mysqli_error($Link_L);
			}
			break;
		}
		if($flag==1)
		{
			$sql_update="update template_i set list='$list',template_name='$template_name' where id='$id'";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_update))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_update . "<br>" . mysqli_error($Link_L);
			}
			break;
		}
		break;
		
	case 'create_template_c':
		$template_name=$j->template_name;
		$id=$j->id;
		$i=0;
		$list="";
		for ($i=0; $i <count($j->list) ; $i++) 
		{ 
			//echo $j->option[$i]."<br>";
			$list=$list.$j->list[$i].",";
		}
		//echo $option;
		$list=chop($list,",");
		//echo $option;
		if(empty($id))
		{
			$sql_insert="insert into template_c (list,template_name) values('$list','$template_name')";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_insert))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_insert . "<br>" . mysqli_error($Link_L);
			}
			break;
		}

		$sql="select id from template_c";
		if ($query_run=mysqli_query($Link_L, $sql))
		{
			//var_dump($id);
			$query_row=mysqli_fetch_assoc($query_run);
			$id_db=$query_row['id'];
			if(!empty($id_db))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
		}

		if($flag==0)
		{
			$sql_insert="insert into template_c (list,template_name) values('$list','$template_name')";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_insert))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_insert . "<br>" . mysqli_error($Link_L);
			}
			break;
		}
		if($flag==1)
		{
			$sql_update="update template_c set list='$list',template_name='$template_name' where id='$id'";
			//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
			
			if ($query_run=mysqli_query($Link_L, $sql_update))
			{
				$response['purpose']="ok";
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo "Error: " . $sql_update . "<br>" . mysqli_error($Link_L);
			}
			break;
		}
		break;


	case 'create_new_activity':
		$st_id=$j->state_id;
		$operator_id=$j->operator_id;
		$ac_id=$j->ac_id;
		$booth_id=$j->booth_id;
		$sl=0;
		$activity_name=$j->activity_name;
		$sql="select sl from activities where operator_id='$operator_id'and booth_id='$booth_id'";
		if($query_run=mysqli_query($Link_L,$sql))
		{
			$query_row=mysqli_fetch_assoc($query_run);
			$sl=$query_row['sl'];
		}
		else
		{
			echo "Error: " . $sql1 . "<br>" . mysqli_error($Link_L);
		}
		$sql="insert into activities (state_id,ac_id,booth_id,activity_name,operator_id) values('$st_id','$ac_id','$booth_id','$activity_name','$operator_id');"."update activities set sl='$sl' where operator_id='$operator_id' and booth_id='$booth_id';";
		//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
		
		if ($query_run=mysqli_multi_query($Link_L, $sql))
		{
			$response['purpose']="ok";
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
		//var_dump($_SESSION);
		//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
	break;


		case 'post_influential':
		header('Content-Type: application/json');
		$profession=$j->profession;
		$previous_election=$j->previous_ele;
		$contributions=$j->contributions;
		$remarks=$j->remarks;
		$coverage=$j->coverage;
		$st_id=$j->st_id;
		$ac_id=$j->ac_id;
		$booth_id=$j->booth_id;
		$voter_id=$j->voter_id;
		$type=$j->type;
		$influential=$j->influential;
		$sql="select voter_id from influential where voter_id='$voter_id'";
		//$sql="select count(voter_id) from influential where voter_id='$voter_id'";
		if($query_run=mysqli_query($Link_L,$sql))
		{
			$count=0;
			$row_count=(mysqli_num_rows($query_run));
			//echo $row_count;
			if($row_count==1)
			{
				$sql1="update influential set st_id='$st_id',ac_id='$ac_id',booth_id='$booth_id',remarks='$remarks',type='$type',profession='$profession',previous_election='$previous_election',contrinutions='$contributions',coverage='$coverage' where voter_id='$voter_id';"."update voter_allocation set influential='$influential' where voter_id='$voter_id'";
				if($query_run=mysqli_multi_query($Link_L,$sql1))
				{
					$response['purpose']="updated";
					echo json_encode($response);
					$count=1;
					break;
				}
				else
				{
					echo "Error: " . $sql1 . "<br>" . mysqli_error($Link_L);	
				}
			}
			if($count==0)
			{
				$sql="insert into influential (st_id,ac_id,booth_id,voter_id,remarks,type,profession,previous_election,contrinutions,coverage) values('$st_id','$ac_id','$booth_id','$voter_id','$remarks','$type','$profession','$previous_election','$contributions','$coverage');"."update voter_allocation set influential='$influential' where voter_id='$voter_id'";
				if($query_run=mysqli_multi_query($Link_L,$sql))
				{
					$response['purpose']="inserted";
					echo json_encode($response);
					break;
				}
				else
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);	
				}
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
		break;


		case 'post_worker':
		header('Content-Type: application/json');
		$profession=$j->profession;
		$remarks=$j->remarks;
		$name=$j->name;
		$st_id=$j->st_id;
		$ac_id=$j->ac_id;
		$booth_id=$j->booth_id;
		$voter_id=$j->voter_id;
		$type=$j->type;
		$worker=$j->worker;
		$sql="select voter_id from worker where voter_id='$voter_id'";
		if($query_run=mysqli_query($Link_L,$sql))
		{
			$count=0;
			$row_count=(mysqli_num_rows($query_run));
			//echo $row_count;
			if($row_count==1)
			{
				$sql1="update worker set st_id='$st_id',ac_id='$ac_id',booth_id='$booth_id',remarks='$remarks',type='$type',profession='$profession',name='$name' where voter_id='$voter_id';"."update voter_allocation set worker='$worker' where voter_id='$voter_id'";
			//$sql1="update worker set st_id='$st_id',ac_id='$ac_id',voter_id='$voter_id',remarks='$remarks',booth_id='$booth_id',type='$type',profession=$profession',name='$name' where voter_id='$voter_id'";
				if($query_run=mysqli_multi_query($Link_L,$sql1))
				{
					$response['purpose']="updated";
					echo json_encode($response);
					$count=1;
					break;
				}
				else
				{
					echo "Error: " . $sql1 . "<br>" . mysqli_error($Link_L);	
				}
			}
			if($count==0)
			{
				$sql="insert into worker (st_id,ac_id,voter_id,booth_id,remarks,type,profession,name) values('$st_id','$ac_id','$voter_id','$booth_id','$remarks','$type','$profession','$name');"."update voter_allocation set worker='$worker' where voter_id='$voter_id'";
				if($query_run=mysqli_multi_query($Link_L,$sql))
				{
					$response['purpose']="inserted";
					echo json_encode($response);
					break;
				}
				else
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);	
				}
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
		
		break;

	case 'post_personinfo':
		$mobile=$j->mobile;
		$dob=$j->dob;
		$caste=$j->caste;
		$voter_id=$j->voter_id;
		$influential=$j->influential;
		$worker=$j->worker;
		$survey=$j->survey;
		$status=$j->status;
		$issues=$j->issues;
		$is_voter=$j->is_voter;
		echo $sql="update voter_allocation set mobile='$mobile',dob='$dob',caste='$caste',influential='$influential',worker='$worker',survey='$survey',issues='$issues',is_voter='$is_voter',status='$status' where voter_id='$voter_id';";
		//$sql="update login set mobile='$mobile',pw='$password' where operator_id='$operator_id' ";
		
		if ($query_run=mysqli_query($Link_L, $sql))
		{
			$response['purpose']="ok";
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
		break;

		//api for post_cast_response


	case 'post_caste_response':
		header('Content-Type: application/json');
		$booth_id=$j->booth_id;
		$voter_id=$j->voter_id;
		$caste=$j->caste;
		$sql="select voter_id from voter_allocation where voter_id='$voter_id'";
		//$sql="select count(voter_id) from influential where voter_id='$voter_id'";
		//var_dump($_SESSION);
		if($query_run=mysqli_query($Link_L,$sql))
		{
			$count=0;
			$row_count=(mysqli_num_rows($query_run));
			//echo $row_count;
			if($row_count==1)
			{
				$sql1="update voter_allocation set st_id='$_SESSION[st_id]',ac_id='$_SESSION[ac_id]',operator_id='$_SESSION[operator_id_login]',caste='$caste',voter_id='$voter_id',booth_id='$booth_id' where voter_id='$voter_id'";
				if($query_run=mysqli_query($Link_L,$sql1))
				{
					$response['purpose']="updated";
					echo json_encode($response);
					$count=1;
					break;
				}
				else
				{
					echo "Error: " . $sql1 . "<br>" . mysqli_error($Link_L);	
				}
			}
			if($count==0)
			{
			$sql1="insert into voter_allocation (st_id,ac_id,operator_id,booth_id,caste,voter_id) values('$_SESSION[st_id]','$_SESSION[ac_id]','$_SESSION[operator_id_login]','$booth_id','$caste','$voter_id')";
				if($query_run=mysqli_query($Link_L,$sql1))
				{
					$response['purpose']="inserted";
					echo json_encode($response);
					break;
				}
				else
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);	
				}
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
		break;
	case 'post_qn_response':
		header('Content-Type: application/json');
		$booth_id=$j->booth_id;
		$voter_id=$j->voter_id;
		$survey=$j->survey;
		$response1="";
		// echo $_SESSION['st_id'];
		//var_dump($x=($j->answer));
		$txt="";
		foreach ($j->answer as $answer)
			{
				$id=($answer->id);
				$response=($answer->response);
				$response_qn="";

				$temp=0;
				foreach ($response as $value)
				{
					if($temp==0)
					{
						$response_qn=$id.":".$response_qn.$value.",";
						$temp=1;
					}
					else
					{
						$response_qn=$response_qn.$value.",";
					}
				}
				//$txt=$txt.$id.":".$response." ;";
				$response1=$response1.chop($response_qn,",").";";
			}   
			$response1=chop($response1,",");
			$txt=$response1;


			//var_dump($txt);
			

		 $sql="select voter_id from voter_response where voter_id='$voter_id'";
		//$sql= "insert ignore into issues (st_id, ac_id, booth_id, voter_id, qn_response) values ('$st_id', '$ac_id', '$booth_id', '$voter_id', '$qn_response')";
		if ($query_run=mysqli_query($Link_L, $sql))
			{
					{
						//echo mysqli_num_rows($query_run);
						$count_row=mysqli_num_rows($query_run);
						if($count_row==0)
						{
							//echo "ok1";
							//echo $x="insert into voter_response (st_id, ac_id, booth_id, voter_id, qn_response) values ('$_SESSION[st_id]', '$_SESSION[ac_id]', '$booth_id', '$voter_id', '$txt')";
							$sql1="insert into voter_response (st_id, ac_id, booth_id, voter_id, qn_response) values ('$_SESSION[st_id]', '$_SESSION[ac_id]', '$booth_id', '$voter_id', '$txt');"."update voter_allocation set survey='$survey' where voter_id='$voter_id';"."update activities set sl=sl+1 where booth_id='$booth_id' and operator_id='$_SESSION[operator_id_login]';";
							if(mysqli_multi_query($Link_L,$sql1))
							{
								$respons['purpose']="inserted";
								echo json_encode($respons);
							}
							else
							{
								echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
							}
						}
						elseif($count_row==1)
						{
							//echo "ok";
							//echo "update voter_response set qn_response='$txt' where voter_id='$voter_id' ";
							$sql2="update voter_response set qn_response='$txt' where voter_id='$voter_id';"."update voter_allocation set survey='$survey' where voter_id='$voter_id'";
							if(mysqli_multi_query($Link_L,$sql2))
							{
								$respons['purpose']="updated";
								echo json_encode($respons);
							}
							else
							{
								echo "Error: " . $sql2 . "<br>" . mysqli_error($Link_L);
							}
						}
						else
						{
							echo json_encode("voter id not match",JSON_PRETTY_PRINT);
						}
					}
			}
		else 
				{
					echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
				}  
		break;



case 'post_issue_response':
		header('Content-Type: application/json');
		$voter_id=$j->voter_id;
		$booth_id=$j->booth_id;
		$top_issues=$j->top_issues;
		$issues1=$j->issues;
		$temp="";
		foreach ($top_issues as $issues)
		{
			$temp=$temp.$issues.",";
		}
		
		$temp=chop($temp,',');
		$top_issues=$temp;
		//var_dump($_SESSION);
		//echo ($temp);
		//var_dump($temp);
		$sql="select voter_id from voter_response where voter_id='$voter_id'";
		//$sql= "insert ignore into issues (st_id, ac_id, booth_id, voter_id, qn_response) values ('$st_id', '$ac_id', '$booth_id', '$voter_id', '$qn_response')";
		if ($query_run=mysqli_query($Link_L, $sql))
			{
				//echo mysqli_num_rows($query_run);
				if(mysqli_num_rows($query_run)==0)
				{
					$sql1="insert into voter_response (st_id, ac_id, booth_id, voter_id, top_issues) values ('$_SESSION[st_id]', '$_SESSION[ac_id]', '$booth_id', '$voter_id', '$top_issues');"."update voter_allocation set issues='$issues1' where voter_id='$voter_id'";
					if(mysqli_multi_query($Link_L,$sql1))
					{
						$response['purpose']="inserted";
						echo json_encode($response,JSON_PRETTY_PRINT);
					}
					else
					{
						echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					}
				}
				elseif(mysqli_num_rows($query_run)==1)
				{
				 $sql2="update voter_response set top_issues='$top_issues' where voter_id='$voter_id' and st_id='$_SESSION[st_id]' and ac_id='$_SESSION[ac_id]';"."update voter_allocation set issues='$issues1' where voter_id='$voter_id'";
					if(mysqli_multi_query($Link_L,$sql2))
					{
						$response['purpose']="updated";
						echo json_encode($response,JSON_PRETTY_PRINT);
					}
					else
					{
						echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					}
				}
				else
				{
					echo "voter id not match";
				}
			}
		else 
			{
				echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
			}	

	break;	

	case 'post_voter_allocation':
		$operator_id=$j->operator_id;
		$st_id=$j->st_id;
		$ac_id=$j->ac_id;
		$booth_id=$j->booth_id;
		$insert=""; 
		$update="";
		$flagd1=10;
		$flagd2=10;
		$flagu=10;
		$flagi=10;
		$temp=0;
		$response_array=array();
		if(count($j->vo)>0)
		{
			foreach ($j->vo as $response)
			{
				$voter_id=$response->voter_id;
				$name=$response->name;
				$age=$response->age;
				$guardian=$response->guardian;
				$gender=$response->gender;
				$address=$response->address;
				$influential=$response->influential;
				$worker=$response->worker;
				$issues=$response->issues;
				$caste=$response->caste;
				$is_voter=$response->is_voter;
				$dob=$response->dob;
				$mobile=$response->mobile;
				$lat=$response->lat;
				$lng=$response->lng;
				$status=$response->status;
				
				$sql1="select * from voter_allocation where voter_id='$voter_id'";
				if($query_run1=mysqli_query($Link_L,$sql1))
				{
					$count_row=mysqli_num_rows($query_run1);
					if($count_row==0)
					{
							$insert="insert into voter_allocation (st_id,ac_id,booth_id,voter_id,name,age,guardian,gender,address,influential,worker,issues,caste,is_voter,dob,mobile,lat,lng,status,operator_id) values ('$st_id','$ac_id','$booth_id','$voter_id','$name','$age','$guardian','$gender','$address','$influential','$worker','$issues','$caste','$is_voter','$dob','$mobile','$lat','$lng','$status','$operator_id');";
							if(mysqli_query($Link_L,$insert))
							{
								$flagi=1;
								$response_array['purpose1']="Inserted in to voter allocation P1!";
								//echo json_encode($response_array,JSON_PRETTY_PRINT);
							}
							else
							{
								$flagi=0;
								echo "Error: " . $insert . "<br>" . mysqli_error($Link_L);
								//break;
							}

							// $insert.= "update login set voters_total=voters_total+'1' where operator_id='$operator_id';";


							$insert="update login set voters_total=voters_total+'1' where operator_id='$operator_id';";
							if(mysqli_query($Link_L,$insert))
							{
								$flagd1=1;
								$response_array['purpose2']="updated login table(total voters increased) P2!";
								//echo json_encode($response_array,JSON_PRETTY_PRINT);
							}
							else
							{
								$flagd1=0;
								echo "Error: " . $insert . "<br>" . mysqli_error($Link_L);
								//break;
							}
		
					}
				else
					{
						$sql14="select * from voter_allocation where voter_id='$voter_id'";
						if($query_run14=mysqli_query($Link_L,$sql14))
						{
							$query_row14=mysqli_fetch_assoc($query_run14);
							$temp_operator_id=$query_row14['operator_id'];

						}

						$update15="update login set voters_total=voters_total-'1' where operator_id='$temp_operator_id';";
						if(mysqli_query($Link_L,$update15))
						{
							$flagd3=1;
							 $response_array['purpose9']="updated login (total voters decresed) P9!";
							 //echo json_encode($response_array,JSON_PRETTY_PRINT);
						}
						else
						{
							$flagd3=0;
							echo "Error: " . $delete . "<br>" . mysqli_error($Link_L);
							//break;
						}

						//update that voter on that id
						$update="update voter_allocation set operator_id='$operator_id' where voter_id='$voter_id';";
						if(mysqli_query($Link_L,$update))
						{
							$flagu=1;
							$response_array['purpose3']="updated voter allocations operator id P3!";
							//echo json_encode($response_array,JSON_PRETTY_PRINT);
						}
						else
						{
							$flagu=0;
							echo "Error: " . $insert . "<br>" . mysqli_error($Link_L);
						}


						$update12="update login set voters_total=voters_total+'1' where operator_id='$operator_id';";
						if(mysqli_query($Link_L,$update12))
						{
							$flagd3=1;
							$response_array['purpose8']="updated login (total voters increased)  P8!";
							//echo json_encode($response_array,JSON_PRETTY_PRINT);
						}
						else
						{
							$flagd3=0;
							echo "Error: " . $delete . "<br>" . mysqli_error($Link_L);
							//break;
						}
					}
				}
				else
				{
					echo "Error: " . $sql1 . "<br>" . mysqli_error($Link_L);
					break;
				}
			}
				


		}
		else
		{
			//$response_array['purpose1']="Inserted!";
			//echo json_encode($response_array,JSON_PRETTY_PRINT);
		}

		//delete query execution


		$delete="";
		if(count($j->vo_d))
		{
			foreach ($j->vo_d as $response1)
			{
				$voter_id=$response1->voter_id;
				$sql="select * from voter_allocation where voter_id='$voter_id'";
				if($query_result=mysqli_query($Link_L,$sql))
				{
					if(!empty($query_result))
					{
						$query_row=mysqli_fetch_assoc($query_result);
						$survey=(int)$query_row['survey'];
						if($survey==1)
						{
							$query_sl="update activities set sl=sl-'1'";
							if(mysqli_query($Link_L,$delete))
							{
								 $response_array['purpose6']="sl decresed in activities p6!";
								 //echo json_encode($response_array,JSON_PRETTY_PRINT);
							}
						}
					}
				}

		$sql13="select * from voter_allocation where voter_id='$voter_id'";
		if($query_run13=mysqli_query($Link_L,$sql13))
				{
					$count_row=mysqli_num_rows($query_run13);
					if($count_row==1)
					{
						$delete="delete from voter_allocation where voter_id='$voter_id';";
						//$delete.="update login set voters_total=voters_total-'1' where operator_id='$operator_id';";
						if(mysqli_query($Link_L,$delete))
							{
								$flagd2=1;
								 $response_array['purpose4']="deleted from voter_allocation P4!";
								 //echo json_encode($response_array,JSON_PRETTY_PRINT);
							}
						else
							{
								$flagd2=0;
								echo "Error: " . $delete . "<br>" . mysqli_error($Link_L);
								break;
							}

							$delete="update login set voters_total=voters_total-'1' where operator_id='$operator_id';";
							if(mysqli_query($Link_L,$delete))
							{
								$flagd3=1;
								 $response_array['purpose5']="updated login (total voters decresed) P5!";
								 //echo json_encode($response_array,JSON_PRETTY_PRINT);
							}
						else
							{
								$flagd3=0;
								echo "Error: " . $delete . "<br>" . mysqli_error($Link_L);
								break;
							}
						}
					}
		}
	}
	$response_array['purpose']="success!";

	echo json_encode($response_array,JSON_PRETTY_PRINT);
	break;
} 
?> 
