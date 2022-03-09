<?php


class Permission 
{

	public static $dbt = "Permissions";


	/*

		ADD
		
	*/


	// Add new permission
	public static function add($name)
	{
		global $dbh;

		$sql = "
				INSERT INTO " . self::$dbt . " 
								(name)
				VALUES 
								(:name)
				";

		try 
		{
			$sth = $dbh->prepare($sql);
  			$sth->bindParam(':name', $name, PDO::PARAM_INT);
  			$sth->execute();
		} 
		catch (PDOException $e) 
		{
			echo $e->getMessage();
		}	
	}


	// Assign permission to role
	public static function toRoleAdd($permission_id, $roleId)
	{
		global $dbh;

		$sql = "
				INSERT INTO Roles_Permissions 
								(roleId, permission_id)
				VALUES 
								(:roleId, :permission_id)
				";
		try 
		{	
			$sth = $dbh->prepare($sql);
  			$sth->bindParam(':roleId', $roleId, PDO::PARAM_INT);
  			$sth->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);
  			$sth->execute();
		} 
		catch (PDOException $e) 
		{	
			echo $e->getMessage();
		}	
	}


	/*

		GET
		
	*/


	// Get all permissions
	public static function getAll()
	{	
		global $dbh;

		$sql = 	"
				SELECT * 
				FROM  " . self::$dbt . "
				";
		try 
		{		
			$sth = $dbh->prepare($sql);
			$sth->execute();	
			$result = $sth->fetchAll(PDO::FETCH_ASSOC); 
			return $result;

		} 
		catch (PDOException $e) 
		{	
			echo $e->getMessage();
		}
	}

	// Get all by group
	public static function getAllFilterByGroupId($group_id)
	{	
		global $dbh;

		$sql = 	"
				SELECT * 
				FROM  " . self::$dbt . "
				WHERE group_id = :group_id
				";

		try 
		{			
			$sth = $dbh->prepare($sql);
  			$sth->bindParam(':group_id', $group_id, PDO::PARAM_INT);
  			$sth->execute();
  			$result = $sth->fetchAll(PDO::FETCH_ASSOC); 
			return $result;

		} 
		catch (PDOException $e) 
		{	
			echo $e->getMessage();
		}
	}	

	// Get groups of permissions
	public static function getAllGroups()
	{	
		global $dbh;

		$sql = 	"
				SELECT * 
				FROM PermissionGroups
				";

		try 
		{	
			$sth = $dbh->prepare($sql);
  			$sth->execute();
  			$result = $sth->fetchAll(PDO::FETCH_ASSOC); 
			return $result;

		} 
		catch (PDOException $e) 
		{	
			echo $e->getMessage();
		}
	}

	// Get permissions by role
	public static function getAllFromRole($roleId)
	{	
		global $dbh;

		$sql = 	"
				SELECT * 
				FROM Roles_Permissions
				WHERE roleId = :roleId
				";

		try 
		{			
			$sth = $dbh->prepare($sql);
  			$sth->bindParam(':roleId', $roleId, PDO::PARAM_INT);
  			$sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC); 
			return $result;

		} 
		catch (PDOException $e) 
		{	
			echo $e->getMessage();
		}
	}

	// Show permission name
	public static function getNameById($permission_id)
	{	
		global $dbh;

		$sql = 	"
				SELECT * 
				FROM Permissions
				WHERE id = :permission_id
				";

		try 
		{			
			$sth = $dbh->prepare($sql);
  			$sth->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);
  			$sth->execute();
  			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $result[0]["name"];
		} 
		catch (PDOException $e) 
		{	
			echo $e->getMessage();
		}
	}

	// Get member's permission
	public static function getByUserIdAndProjectId($user_id, $ProjectId)
	{	
		global $dbh;

		$sql = 	"
				SELECT 
						Permissions.name as permission_name
				FROM Members

				INNER JOIN Roles 
						ON Members.roleId = Roles.id
				INNER JOIN Projects 
						ON Members.ProjectId = Projects.id
				INNER JOIN Roles_Permissions 
						ON Roles.id = Roles_Permissions.roleId
				INNER JOIN Permissions 
						ON Roles_Permissions.permission_id = Permissions.id

				WHERE Members.user_id = :user_id
				AND Projects.id = :ProjectId
				";
		try 
		{			
			$sth = $dbh->prepare($sql);
  			$sth->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  			$sth->bindParam(':ProjectId', $ProjectId, PDO::PARAM_INT);
  			$sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC); 
return $result;

		} 
		catch (PDOException $e) 
		{	
			echo $e->getMessage();
		}
	}

}
