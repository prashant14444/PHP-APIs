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
$Param = $_GET;

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
switch ($Param['purpose']) 
{

	//activity selection api---  http://localhost/api/api.php?purpose=get_activities

	 case 'get_activities':  // php_Param = "get_count_of_Remaining_Jobs" ;	
	 			header('Content-Type: application/json');
	 		 	 $operator_id=$_SESSION['operator_id_login'];
				//$sessioN_operator_id=$_SESSION['operator_id_login'];
				 $st_id=$_SESSION['st_id'];
				 $ac_id=$_SESSION['ac_id'];
			//echo "$session_operator_id=".$session_operator_id."<br>";
				$sql = "select * from activities where operator_id= '$operator_id' and state_id='$st_id' and ac_id='$ac_id'";
			if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$response=array();
				$i=0;	
				//echo "ok";
				$count=0;
				while($query_row=mysqli_fetch_assoc($query_run))
				{
					$booth_id_db=($query_row['booth_id']);
					$activity_name_db=($query_row['activity_name']);
					$sl_db=($query_row['sl']);

					$response[$i]['booth_id']=$booth_id_db;
					$response[$i]['sl']=$sl_db;
					$response[$i]['activity_name']=$activity_name_db;
					$count=1;
					

					$sql1 = "SELECT voter_id FROM voter_allocation where booth_id='$booth_id_db' and operator_id='$_SESSION[operator_id_login]'";
					if ($count_of_rows=mysqli_query($Link_L, $sql1))
					{
						$total_rows=mysqli_num_rows($count_of_rows);
						$response[$i]['count']=$total_rows;
					}
					else
					{
						echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
					}
					$i++;
				}
				if($count==0)
				{
					print "Wrong Operator id!";
				}
			}
	else 
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}	
		if($query_run) {
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else 
			echo "Error";

		//var_dump($_SESSION);
	break;




		//api for get_my_booths

	case 'get_my_booths':
			header('Content-Type: application/json');
			//$sessioN_state_id=$_SESSION['state_id'];
			 $sessioN_operator_id=$_SESSION['operator_id_login'];
			 //echo $sessioN_operator_id;
			//$sessioN_ac_id=$_SESSION['ac_id'];
			$sql="select booth_id from voter_allocation where operator_id = '$sessioN_operator_id'";
			if($query_run=mysqli_query($Link_L, $sql))
			{
				$i=0;
				while($query_row=mysqli_fetch_assoc($query_run))
				{
					$response=array();
					$booth_id_db=$query_row['booth_id'];
					$sql_temp=" select booth_id, booth_name,st_id,ac_id from booth_list where st_id ='$_SESSION[st_id]' and ac_id='$_SESSION[ac_id]'";
					//$sql_temp=" select booth_id, booth_name,st_id,ac_id from booth_list where booth_id ='$_SESSION[st_id]'";
					if($query_run_temp=mysqli_query($Link_L, $sql_temp))
					{
						while($query_row_temp=mysqli_fetch_assoc($query_run_temp))
						{
							$response[$i]['booth_id']=$query_row_temp['booth_id'];
							$response[$i]['booth_name']=$query_row_temp['booth_name'];
							$response[$i]['state_id']=$query_row_temp['st_id'];
							$response[$i]['ac_id']=$query_row_temp['ac_id'];
							$i++;
						}
						echo json_encode($response,JSON_PRETTY_PRINT);
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



				//api for get_voter_list by booth_id
	case 'get_voter_list';
		header('Content-Type: application/json');
		$booth_id=$Param['booth_id'];
		$sql="select * from voter_allocation where operator_id = '$_SESSION[operator_id_login]' and booth_id = '$booth_id'";
		//$sql="select * from t_u05_1_sample where owner_id = '$opertor_id' and booth_id = '$booth_id'";
		if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$i=0;
				$response=array();
				//echo "ok";
				$count=0;
				while($query_row=mysqli_fetch_assoc($query_run))
				{
					//echo "i=".$i;
						$name_db=($query_row['name']);
						$age_db=($query_row['age']);
						$gender_db=($query_row['gender']);
						$guardian_db=($query_row['guardian']);
						$guardian_db=chop($guardian_db," ");
						$is_voter_db=($query_row['voter_id']);
						$address_db=($query_row['address']);
						$survey_db=($query_row['survey']);

						$response[$i]['name']=$name_db;
						$response[$i]['age']=$age_db;
						$response[$i]['guardian']=$guardian_db;
						$response[$i]['gender']=$gender_db;
						$response[$i]['voter_id']=$is_voter_db;
						$response[$i]['address']=$address_db;
						$response[$i]['survey']=$survey_db;
						$i++;
				}
			}
	else 
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}	
		if($query_run) {
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else 
			echo "Error";
	break;

//api  get_voter
	case 'get_voter';
		header('Content-Type: application/json');
		$voter_id=$Param['voter_id'];
		//$voter_id="mp11";
		$sql="select * from voter_allocation where  voter_id = '$voter_id'";
		//$sql="select * from t_u05_1_sample where  voter_id = '$voter_id'";
		if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$i=0;
				$response=array();
				//echo "ok";
				$count=0;
				while($query_row=mysqli_fetch_assoc($query_run))
				{
					$voter_id_db=($query_row['voter_id']);
					$booth_id_db=($query_row['booth_id']);
					$ac_id_db=$query_row['ac_id'];
					$state_id_db=($query_row['st_id']);
					$name_db=($query_row['name']);
					$age_db=($query_row['age']);
					$guardian_db=($query_row['guardian']);
					$gender_db=($query_row['gender']);
					$address_db=($query_row['address']);
					$influential_db=($query_row['influential']);
					$worker_db=($query_row['worker']);
					$issues_db=($query_row['issues']);
					$caste_db=($query_row['caste']);
					$status_db=($query_row['status']);
					$survey_db=($query_row['survey']);
					$is_voter_db=($query_row['is_voter']);
					$dob_db=($query_row['dob']);
					$mobile_db=($query_row['mobile']);
					$lat_db=($query_row['lat']);
					$lng_db=($query_row['lng']);
					$count=1;


					$response['st_id']=$state_id_db;
					$response['booth_id']=$booth_id_db;
					$response['ac_id']=$ac_id_db;
					$response['name']=$name_db;
					$response['age']=$age_db;
					$response['guardian']=$guardian_db;
					$response['gender']=$gender_db;
					$response['address']=$address_db;
					$response['influential']=$influential_db;
					$response['status']=$status_db;
					$response['survey']=$survey_db;
					$response['worker']=$worker_db;
					$response['issues']=$issues_db;
					$response['caste']=$caste_db;
					$response['is_voter']=$is_voter_db;
					$response['dob']=$dob_db;
					$response['mobile']=$mobile_db;
					$response['lat']=$lat_db;
					$response['lng']=$lng_db;
					$i++;
				}
				if($count==0)
				{
					print "No Voter Exists!";
				}
			}
	else 
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}	
		if($query_run) {
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else 
			echo "Error";
	break;


	case 'get_influencer':
		header('Content-Type: application/json');
		$voter_id=$Param['voter_id'];
		$sql=" select * from influential where  voter_id = '$voter_id' ";
		if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$i=0;
				$response=array();
				//echo "ok";
				$count=0;
				while($query_row=mysqli_fetch_assoc($query_run))
				{
					$state_id_db=($query_row['st_id']);
					$type_db=($query_row['type']);
					$coverage_db=($query_row['coverage']);
					$profession_db=($query_row['profession']);
					$previous_election_db=($query_row['previous_election']);
					$contrinutions_db=($query_row['contrinutions']);
					$remarks_db=($query_row['remarks']);
					$booth_id_db=($query_row['booth_id']);
					$ac_id_db=$query_row['ac_id'];
					$voter_id_db=$query_row['voter_id'];
					$count=1;

					$response[$i]['state_id']=$state_id_db;
					$response[$i]['type']=$type_db;
					$response[$i]['coverage']=$coverage_db;
					$response[$i]['profession']=$profession_db;
					$response[$i]['previous_ele']=$previous_election_db;
					$response[$i]['contributions']=$contrinutions_db;
					$response[$i]['remarks']=$remarks_db;
					$response[$i]['booth_id']=$booth_id_db;
					$response[$i]['voter_id']=$voter_id_db;
					$response[$i]['ac_id']=$ac_id_db;
					$i++;
				}
				if($count==0)
				{
					print "Wrong Oprator id!";
				}
			}
	else 
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}	
		if($query_run) {
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else 
			echo "Error";
	break;



//api for get_worker 

case 'get_worker':
		header('Content-Type: application/json');
		$voter_id=$Param['voter_id'];
		$sql=" select * from worker where  voter_id = '$voter_id' ";
		if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$i=0;
				$response=array();
				//echo "ok";
				$count=0;
				while($query_row=mysqli_fetch_assoc($query_run))
				{
					$state_id_db=($query_row['st_id']);
					$type_db=($query_row['type']);
					$name_db=($query_row['name']);
					$profession_db=($query_row['profession']);
					$remarks_db=($query_row['remarks']);
					$booth_id_db=($query_row['booth_id']);
					$ac_id_db=$query_row['ac_id'];
					$voter_id_db=$query_row['voter_id'];
					$count=1;

					$response[$i]['st_id']=$state_id_db;
					$response[$i]['type']=$type_db;
					$response[$i]['name']=$name_db;
					$response[$i]['profession']=$profession_db;
					$response[$i]['remarks']=$remarks_db;
					$response[$i]['booth_id']=$booth_id_db;
					$response[$i]['voter_id']=$voter_id_db;
					$response[$i]['ac_id']=$ac_id_db;
					$i++;
				}
				if($count==0)
				{
					print "Wrong Voter id!";
				}
			}
	else 
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}	
		if($query_run) {
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else 
			echo "Error";
	break;

//api for get_survey


case 'get_question_survey':
	header('Content-Type: application/json');
	$voter_id=$Param['voter_id'];
	$qn_array=array();
	$qn_array1=array();
	$response_temp=array();
	$k=0;
	$sql="select qn_response,voter_id from voter_response where voter_id='$voter_id'";
	if($query_run=mysqli_query($Link_L, $sql))
	{
		$query_row=mysqli_fetch_assoc($query_run);
		$qn_response=$query_row['qn_response'];
		if(!empty($query_row['voter_id']))
		{
			$not_voter_id=0;
			//var_dump($qn_response);
			if (!empty($qn_response))
			{
				$qn_response=chop($qn_response,";");
				$qn_response=explode(";", $qn_response);
				//$not_voter_id=0;
				foreach ($qn_response as $qn)
				{
					$i=0;
					$qn=explode(":", $qn);
					$qn_id=$qn[0];
					$qn_id=trim($qn_id);
					$qn_text=$qn[1];
					$qn_text=trim($qn_text);
					$qn_text=chop($qn_text,",");
					$qn_text=explode(",", $qn_text);
					$response_temp['answer'][$qn_id]=$qn_text;
				}
			}
			else
			{
				$not_voter_id=1;
			}
		}
		else
		{

				$sql="select template_id from  template_allocation_q where operator_id = '$_SESSION[operator_id_login]'";
				if ($query_run=mysqli_query($Link_L, $sql))
					{ 
						$j=0;
						$i=0;
						//echo "ok";
						$response=array();
						$count=0;
						while($query_row=mysqli_fetch_assoc($query_run))
						{
							$template_id_db=$query_row['template_id'];
							$sql_temp1="select qn_list from template_q where id = '$template_id_db'";
							if ($query_run_temp1=mysqli_query($Link_L, $sql_temp1))
							{
								$query_row_temp=mysqli_fetch_assoc($query_run_temp1);
								$list_i_id=$query_row_temp['qn_list'];
								$list_i_id=explode(",", $list_i_id);
								foreach($list_i_id as $list) 
								{ 
									$id_q=$list;
									$sql_temp2="select * from list_q where id = '$id_q'";
									if ($query_run_temp2=mysqli_query($Link_L,$sql_temp2))
									{
										$query_run_temp2=mysqli_fetch_assoc($query_run_temp2);
										$id_db=$query_run_temp2['id'];
										$text_db=$query_run_temp2['text'];

										$response[$i]['id']=$id_db;
										$response[$i]['answer'][]=null;
										$response[$i]['title']=$query_run_temp2['title'];
										$response[$i]['text']=$text_db;
										$response[$i]['type']=$query_run_temp2['type'];
										$options_db=$query_run_temp2['options'];
										$options_db=chop($options_db,".");
										$options_db=explode(",", $options_db);
										$response[$i]['options']=$options_db;
										$i++;
									}
									else
										{
											echo "Error: " . $sql_temp2 . "<br>" . mysqli_error($Link_L);
										}
								}
							}
						}
						echo json_encode($response,JSON_PRETTY_PRINT);
					}
				else
				{
					echo "Error: " . $sql_temp1 . "<br>" . mysqli_error($Link_L);
				}
			break;
		}
	}
	else
	{
		echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		break;
	}
	//print_r($response_temp);
	$sql="select template_id from  template_allocation_q where operator_id = '$_SESSION[operator_id_login]'";
	if ($query_run=mysqli_query($Link_L, $sql))
		{ 
			$j=0;
			$i=0;
			//echo "ok";
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$template_id_db=$query_row['template_id'];
				$sql_temp1="select qn_list from template_q where id = '$template_id_db'";
				if ($query_run_temp1=mysqli_query($Link_L, $sql_temp1))
				{
					$query_row_temp=mysqli_fetch_assoc($query_run_temp1);
					$list_i_id=$query_row_temp['qn_list'];
					$list_i_id=explode(",", $list_i_id);
					foreach($list_i_id as $list) 
					{ 
						$id_q=$list;
						$sql_temp2="select * from list_q where id = '$id_q'";
						if ($query_run_temp2=mysqli_query($Link_L,$sql_temp2))
						{
							$query_run_temp2=mysqli_fetch_assoc($query_run_temp2);
							$id_db=$query_run_temp2['id'];
							$text_db=$query_run_temp2['text'];

							$response[$i]['id']=$id_db;
							if($not_voter_id==0)
							{
								if(array_key_exists($id_q, $response_temp['answer']))
									$response[$i]['answer']=$response_temp['answer'][$id_q];
								else
									$response[$i]['answer'][]=null;
							}
							else
								$response[$i]['answer'][]=null;
							$response[$i]['title']=$query_run_temp2['title'];
							$response[$i]['text']=$text_db;
							$response[$i]['type']=$query_run_temp2['type'];
							$options_db=$query_run_temp2['options'];
							$options_db=chop($options_db,".");
							$options_db=explode(",", $options_db);
							$response[$i]['options']=$options_db;
							$i++;
						}
						else
							{
								echo "Error: " . $sql_temp2 . "<br>" . mysqli_error($Link_L);
							}
					}
				}
			}
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
	else
	{
		echo "Error: " . $sql_temp1 . "<br>" . mysqli_error($Link_L);
	}
break;

case 'get_caste_survey':
		header('Content-Type: application/json');
		$x=array();
		//$voter_id=$Param['voter_id'];
		//$sql="select * from voter_response where voter_id = '$voter_id'";
		$sql="select template_id from  template_allocation_c where operator_id = '$_SESSION[operator_id_login]'";
		if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$j=0;
				$i=0;
				//echo "ok";
				$response=array();
				$count=0;
				($query_row=mysqli_fetch_assoc($query_run));
				{
					//echo "in";
					$template_id_db=$query_row['template_id'];
					//echo $template_id_db;
					$sql_temp1="select list from template_c where id = '$template_id_db'";
					if ($query_run_temp1=mysqli_query($Link_L, $sql_temp1))
					{
						//echo "in1";
						$query_row_temp=mysqli_fetch_assoc($query_run_temp1);
						$list_i_id=$query_row_temp['list'];
						$list_i_id=chop($list_i_id,",");
						//echo ($list_i_id);
						$list_i_id=explode(",", $list_i_id);
						//echo ($list_i_id);
						foreach ($list_i_id as $id) 
						{ 
							$id_c=$id;
							$sql_temp2="select * from list_c where id = '$id_c'";
							if ($query_run_temp2=mysqli_query($Link_L,$sql_temp2))
							{
								$query_run_temp2=mysqli_fetch_assoc($query_run_temp2);
								$id_db=$query_run_temp2['id'];
								$text_db=$query_run_temp2['text'];
								if(!empty($text_db))
								{
									$text_db=chop($text_db,".");
									$text_db=chop($text_db,",");

									$text_db=explode(",", $text_db);
									$response[$i]['text']=$text_db;
								}
								else
								{
									$response[$i]['text']=$x;
								}

								$response[$i]['id']=$id_db;
								$response[$i]['type']=$query_run_temp2['type'];
								$i++;
							}
							else
								{
									echo "Error: " . $sql_temp2 . "<br>" . mysqli_error($Link_L);
								}
						}
					}
				}

				echo json_encode($response,JSON_PRETTY_PRINT);
			//	echo json_encode($response);
			}
		else
		{
			echo "Error: " . $sql_temp1 . "<br>" . mysqli_error($Link_L);
		}
	break;



case 'get_issue_survey':
		//header('Content-Type: application/json');
		//echo $_SESSION['operator_id_login'];
		//$_SESSION['operator_id_login']=1;
		//echo $_SESSION['operator_id_login'];
	$voter_id=$Param['voter_id'];
	$qn_array=array();
	$qn_array1=array();
	$k=0;
	//$sql="select * from voter_response where voter_id = '$voter_id'";
	$sql="select top_issues from voter_response where voter_id='$voter_id'";
	if($query_run=mysqli_query($Link_L, $sql))
	{
		$query_row=mysqli_fetch_assoc($query_run);
		$top_issues1=$query_row['top_issues'];
		$top_issues1=chop($top_issues1,",");
		$top_issues1=chop($top_issues1," ");
		$top_issues1=chop($top_issues1,",");
		$top_issues1=chop($top_issues1," ");
		$top_issues1=explode(",", $top_issues1);
		//var_dump($top_issues);
		$top_issues_temp=array();
		foreach ($top_issues1 as $value)
		{
			array_push($top_issues_temp, $value);
		}
		//print_r ($top_issues_temp);
		//break;
		$top_issues=$top_issues_temp;
		//print_r($top_issues);
	}
		//var_dump($top_issues);
		//var_dump($qn_array1);
	$x=array();
		$sql="select template_id from  template_allocation_i where operator_id = '$_SESSION[operator_id_login]'";
		if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$j=0;
				$i=0;
				//echo "ok";
				$response=array();
				$count=0;
				($query_row=mysqli_fetch_assoc($query_run));
				{
					//echo "in";
					$template_id_db=$query_row['template_id'];
					//echo $template_id_db;
					$sql_temp1="select list from template_i where id = '$template_id_db'";
					if ($query_run_temp1=mysqli_query($Link_L, $sql_temp1))
					{
						//echo "in1";
						$query_row_temp=mysqli_fetch_assoc($query_run_temp1);
						$list_i_id=$query_row_temp['list'];
						//echo ($list_i_id);
						$list_i_id=explode(",", $list_i_id);
						//echo ($list_i_id);
						foreach ($list_i_id as $id) 
						{ 
							$id_i=$id;
							$sql_temp2="select * from list_i where id = '$id_i'";
							if ($query_run_temp2=mysqli_query($Link_L,$sql_temp2))
							{
								$query_run_temp2=mysqli_fetch_assoc($query_run_temp2);
								$id_db=$query_run_temp2['id'];
								$text_db=$query_run_temp2['text'];
								//echo in_array($id_db, $top_issues);
								if(!empty($text_db))
								{
									$text_db=chop($text_db,".");
									$text_db=chop($text_db,",");
									$text_db=explode(",", $text_db);
									$response[$i]['text']=$text_db;
								}
								else
								{
									$response[$i]['text']=$x;
								}
								$index=array_search($id_db,$top_issues);
								if($index>=0)
								{
									//echo "ok";
									$response[$i]['top_issues']=(string)$index;
								}
								else
								{
									$response[$i]['top_issues']="";
									$response[$i]['top_issues']=$top_issues;
								}

								$response[$i]['id']=$id_db;
								$response[$i]['type']=$query_run_temp2['type'];
								$i++;
							}
							else
								{
									echo "Error: " . $sql_temp2 . "<br>" . mysqli_error($Link_L);
								}
						}
					}
				}

				echo json_encode($response,JSON_PRETTY_PRINT);
				//echo json_encode($response);
			}
		else
		{
			echo "Error: " . $sql_temp1 . "<br>" . mysqli_error($Link_L);
		}
	break;


//api for getting operator list  get_operator_list
	case 'get_operator_list':
		header('Content-Type: application/json');
		$sql="select * from login";
		if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['name']=$query_row['name'];
				$response[$i]['email']=$query_row['email'];
				$response[$i]['operator_id']=$query_row['operator_id'];
				$response[$i]['st_id']=$query_row['st_id'];
				$response[$i]['ac_id']=$query_row['ac_id'];
				$response[$i]['mobile']=$query_row['mobile'];
				$response[$i]['status']=$query_row['status'];
				$response[$i]['type']=$query_row['type'];
				$response[$i]['t_c']=$query_row['t_c'];
				$response[$i]['t_q']=$query_row['t_q'];
				$response[$i]['t_i']=$query_row['t_i'];
				$response[$i]['pw']=$query_row['pw'];
				$response[$i]['voters_total']=$query_row['voters_total'];
				$response[$i]['voters_done']=$query_row['voters_done'];
				$i++;
				$count=1;
			}
			if($count==0)
			{
				echo "No Operator Found";
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
		break;
// apii for  getting question list       purpose=get_question_list

		case 'get_question_list':
			header('Content-Type: application/json');
			$sql="select * from list_q";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['id']=$query_row['id'];
				$response[$i]['title']=$query_row['title'];
				$response[$i]['text']=$query_row['text'];
				$response[$i]['type']=$query_row['type'];
				$options_db=$query_row['options'];
				if(!empty($options_db))
				{
					$options_db=chop($options_db,".");
					$options_db=explode(",", $options_db);
					$response[$i]['options']=$options_db;
				}
				else
				{
					$x=array();
					$response[$i]['options']=$x;
				}
				$i++;
				$count=1;
			}
			if($count==0)
			{
				
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;




//api for get_issue_list


		case 'get_issue_list':
		header('Content-Type: application/json');
		$sql="select * from list_i";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['id']=$query_row['id'];
				$text_db=$query_row['text'];
				//$text_db=explode(",", $text_db);
				$response[$i]['text']=$text_db;
				$type_db=$query_row['type'];
				//echo $type_db."<br>";
				$response[$i]['type']=$type_db;
				$i++;
				$count=1;
			}
			if($count==0)
			{
				echo "No Issue Found In Database";
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;


//api for get_caste_list


		case 'get_caste_list':
		header('Content-Type: application/json');
		$sql="select * from list_c";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['id']=$query_row['id'];
				$text_db=$query_row['text'];
				//$text_db=explode(",", $text_db);
				$response[$i]['text']=$text_db;
				$response[$i]['type']=$query_row['type'];
				$i++;
				$count=1;
			}
			if($count==0)
			{
				echo "No Entry Found In Database";
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;


	//api for get_templete_list

	case 'get_question_template_list':
		//header('Content-Type: application/json');
		$sql="select * from template_q";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['template_name']=$query_row['template_name'];
				$response[$i]['id']=$query_row['id'];
				$qn_list_db=$query_row['qn_list'];
				$qn_list_db=chop($qn_list_db,',');
				$qn_list_db=chop($qn_list_db,' ');
				$qn_list_db=explode(",", $qn_list_db);
				$response[$i]['list']=$qn_list_db;
				$i++;
				$count=1;
			}
			if($count==0)
			{
				//echo "No Template Name Found";
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;

	case 'get_issue_template_list':
		header('Content-Type: application/json');

		$sql="select * from template_i";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['template_name']=$query_row['template_name'];
				$response[$i]['id']=$query_row['id'];
				$list_db=$query_row['list'];
				$list_db=chop($list_db,',');
				$list_db=chop($list_db,' ');
				$list_db=explode(",", $list_db);
				$response[$i]['list']=$list_db;
				$i++;
				$count=1;
			}
			if($count==0)
			{
				echo "No Template Name Found";
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;

	case 'get_caste_template_list':
		header('Content-Type: application/json');

		$sql="select * from template_c";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['template_name']=$query_row['template_name'];
				$response[$i]['id']=$query_row['id'];
				$list_db=$query_row['list'];
				$list_db=chop($list_db,',');
				$list_db=chop($list_db,' ');
				$list_db=explode(",", $list_db);
				$response[$i]['list']=$list_db;
				$i++;
				$count=1;
			}
			if($count==0)
			{
				echo "No Template Name Found";
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;

		//api for getting booth list      get_booth_list

	case 'get_booth_list':
		header('Content-Type: application/json');
		$st_id=$Param['st_id'];
		$ac_id=$Param['ac_id'];
		//echo $_SESSION['st_id'];
		//echo $_SESSION['ac_id'];
		//$sql="select booth_name,booth_id from booth_list where st_id='$_SESSION[st_id]' and ac_id='$_SESSION[st_id]'";
		$sql="select booth_name,booth_id from booth_list where st_id='$st_id' and ac_id='$ac_id'";
			//$sql="select booth_id from t_u05_1_sample where state_id='$st_id'";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['booth_name']=$query_row['booth_name'];
				$response[$i]['booth_id']=$query_row['booth_id'];
				$i++;
				$count=1;
			}
			if($count==0)
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;
//api for geeting voter list by booth id to admin     get_voter_list_by_booth_id


	case 'get_voter_list_by_booth_id';
		//header('Content-Type: application/json');
		$booth_id=$Param['booth_id'];
		//$booth_id=2;
			//$sql="select * from voter where booth_id = '$booth_id'";
		//$sql="select * from voter where booth_id = '$booth_id'";
		//$sql="SELECT voter.voter_id,voter.name,voter.age,voter.gender,voter.guardian,voter.address,voter_allocation.operator_id,voter_allocation.survey,voter_allocation.booth_id FROM voter LEFT JOIN voter_allocation ON voter.voter_id=voter_allocation.voter_id and voter_allocation.booth_id='$booth_id' order by voter_allocation.operator_id desc";

		$sql="SELECT voter.voter_id,voter.name,voter.age,voter.gender,voter.guardian,voter.address,voter_allocation.operator_id,voter_allocation.survey,voter_allocation.booth_id FROM voter LEFT JOIN voter_allocation ON voter.voter_id=voter_allocation.voter_id order by voter_allocation.operator_id desc";
		if ($query_run=mysqli_query($Link_L, $sql))
			{ 
				$i=0;
				$response=array();
				//echo "ok";
				$count=0;
				while($query_row=mysqli_fetch_assoc($query_run))
				{
						$name_db=($query_row['name']);
						$age_db=($query_row['age']);
						$gender_db=($query_row['gender']);
						$guardian_db=($query_row['guardian']);
						$guardian_db=chop($guardian_db," ");
						$is_voter_db=($query_row['voter_id']);
						$address_db=($query_row['address']);
						$operator_id_db=($query_row['operator_id']);
						$status_db=($query_row['survey']);
						$booth_id_db=($query_row['booth_id']);

						$response[$i]['name']=$name_db;
						$response[$i]['age']=$age_db;
						$response[$i]['guardian']=$guardian_db;
						$response[$i]['gender']=$gender_db;
						$response[$i]['voter_id']=$is_voter_db;
						$response[$i]['address']=$address_db;
						$response[$i]['operator_id']=$operator_id_db;
						$response[$i]['booth_id']=$booth_id_db;
						$response[$i]['survey']=$status_db;
						$i++;
						$count=1;
				}
				//if($count==0)
				{
				//	print "Wrong Voter id!";
				}
			}
	else 
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}	
		if($query_run) {
			echo json_encode($response,JSON_PRETTY_PRINT);
		}
		else 
			echo "Error";
	break;

	case 'get_st_list':
			header('Content-Type: application/json');
			$sql="select st_id,name from st_id ";
			//$sql="select booth_id from t_u05_1_sample where state_id='$st_id'";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['st_id']=$query_row['st_id'];
				$response[$i]['name']=$query_row['name'];
				$i++;
				$count=1;
			}
			if($count==0)
			{
				echo "No state id Found";
			}
			else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;
	case 'get_ac_list':
			header('Content-Type: application/json');
			$st_id=$Param['st_id'];
			$sql="select ac_id,name from ac_id where st_id='$st_id'";
			//$sql="select booth_id from t_u05_1_sample where state_id='$st_id'";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['ac_id']=$query_row['ac_id'];
				$response[$i]['name']=$query_row['name'];
				$i++;
				$count=1;
			}
			//if($count==0)
			{
				//echo "No ac id Found";
			}
			//else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
			break;



		case 'get_type':
			header('Content-Type: application/json');
			$sql="select type,id from type";
			//$sql="select booth_id from t_u05_1_sample where state_id='$st_id'";
			if($query_run=mysqli_query($Link_L,$sql))
		{
			$i=0;
			$response=array();
			$count=0;
			while($query_row=mysqli_fetch_assoc($query_run))
			{
				$response[$i]['id']=$query_row['id'];
				$response[$i]['type']=$query_row['type'];
				$i++;
				$count=1;
			}
			//if($count==0)
			{
			//	echo "No type Found";
			}
			//else
			{
				echo json_encode($response,JSON_PRETTY_PRINT);
			}
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($Link_L);
		}
	break;
}
?>
