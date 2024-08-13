<?php
header('Content-Type: application/json');

//databse connection
$con = new mysqli('localhost','root','','blogs');
	if($con->connect_error){
		die('Connection failed'.$con->connect_error);
	}


//get the http method
$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/',trim($_SERVER['REQUEST_URI'],"/"));
$id = isset($uri[2]) ? intval($uri[2]) : 0;

switch($method){
	case 'GET' : 
			if($id){
				getPost($con,$id);
			}else{
				getPosts($con);
			}
			break;
	case 'POST' : 
			createPost($con);
			break;
	case 'PUT' :
			updatePost($con,$id);
			break;
	case 'DELETE' : 
			deletePost($con,$id);
			break;
	default:
		 http_response_code(405);
         echo json_encode(['error' => 'Method Not Allowed']);
         break;
}

//get all posts
function getPosts($con){
	$result = $con->query("SELECT *FROM posts");
	$posts = $result->fetch_all(MYSQLI_ASSOC);
	if(count($posts)){
		echo json_encode($posts);
	}else{
		echo json_encode(['message' =>'No Posts!']);
	}

}

//create post
function createPost($con){
	$data = json_decode(file_get_contents('php://input'),true);
	if(isset($data['title']) && isset($data['content']) && isset($data['author'])){
		$result = $con->prepare("INSERT INTO posts(title,content,author) VALUES(?,?,?)");
		$result->bind_param('sss',$data['title'],$data['content'],$data['author']);
		$result->execute();
		$id = $con->insert_id;
		$post = $con->query("SELECT *FROM posts where id = $id");
		$getPost = $post->fetch_assoc();
		http_response_code(201);
		echo json_encode(['id' => $id,'message'=>'Post Created!','post'=> $getPost]);
		$result->close();
	}else{
		http_response_code(400);
		echo json_encode(['message' =>'please add all input']);
	}
}

//view post by id
function getPost($con,$id){
	$view_query = $con->prepare("SELECT *FROM posts where id = ?");
	$view_query->bind_param('i',$id);
	$view_query->execute();
	$result = $view_query->get_result();
	$post = $result->fetch_assoc();
	
		if($post){
			echo json_encode($post);
		}else{
			http_response_code(404);
			echo json_encode(['error'=>'Post Not Found']);
		}
	
	$view_query->close();
}

//update post
function updatePost($con,$id){
	$data = json_decode(file_get_contents('php://input'),true);
	if($id && (isset($data['title']) && isset($data['content']) && isset($data['author']))){
		$result = $con->prepare("UPDATE posts SET title = ? ,content = ? , author = ?  where id= ?");
		$result->bind_param('sssi',$data['title'],$data['content'],$data['author'],$id);
		$result->execute();
		$post = $con->query("SELECT *FROM posts where id = $id");
		$getPost = $post->fetch_assoc();
		if($result->affected_rows){
			http_response_code(200);
			echo json_encode(['message' => 'Post Updated!','post'=>$getPost]);
		}else{
			http_response_code(400);
			echo json_encode(['message' => 'Post Not Found!']);
		}
	
		$result->close();
	}else{
		http_response_code(400);
		echo json_encode(['message' =>'please add all input']);
	}
}

//delete post
function deletePost($con,$id){
	$result = $con->prepare("DELETE FROM posts where id= ?");
	$result->bind_param('i',$id);
	$result->execute();
	if($result->affected_rows){
		http_response_code(200);
		echo json_encode(['message' => 'Post Deleted!']);
	}else{
		http_response_code(400);
		echo json_encode(['message' => 'Post Not Found!']);
	}
	
	$result->close();
}
?>