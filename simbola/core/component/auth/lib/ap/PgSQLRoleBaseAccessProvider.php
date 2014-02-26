<?php
namespace simbola\core\component\auth\lib\ap;
/**
 * Description of MySQLRoleBaseAccessProvider
 *
 * @author Faraj
 */
class PgSQLRoleBaseAccessProvider extends DBRoleBaseAccessProvider {

    public function itemCreate($name, $type) {
        if (!$this->itemExist($name)) {
            $sql = "INSERT INTO {$this->getTableName(SELF::TBL_ITEM)} (item_id,item_name,item_type)
                        VALUES(NEXTVAL('{$this->getTableName(SELF::TBL_ITEM)}_seq'),'{$name}','{$type}')";
            $this->dbExecute($sql);
        }
    }

    public function userCreate($user_name) {
        $sql = "INSERT INTO {$this->getTableName(SELF::TBL_USER)} (user_id,user_name,user_password)
                    VALUES(NEXTVAL('{$this->getTableName(SELF::TBL_USER)}_seq'),'{$user_name}',md5('{$user_name}'))";
        $this->dbExecute($sql);
    }

    public function createTblAuthUser() {
        $sql = "CREATE TABLE {$this->getTableName(SELF::TBL_USER)} (                     
                    user_id INTEGER PRIMARY KEY,
                    user_active BOOLEAN DEFAULT TRUE,
                    user_name TEXT UNIQUE,
                    user_password TEXT
                );
                CREATE SEQUENCE {$this->getTableName(SELF::TBL_USER)}_seq;
                ALTER TABLE {$this->getTableName(SELF::TBL_USER)} 
                    ALTER COLUMN user_id 
                    SET DEFAULT NEXTVAL('{$this->getTableName(SELF::TBL_USER)}_seq')";
        $this->dbExecute($sql);
    }
    
    public function createTblAuthSession() {        
        $sql = "CREATE TABLE {$this->getTableName(SELF::TBL_SESSION)} (  
                    id INTEGER PRIMARY KEY,
                    create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    client_addr VARCHAR(50),
                    user_id INTEGER,
                    skey TEXT,
                    description TEXT
                );
                CREATE SEQUENCE {$this->getTableName(SELF::TBL_SESSION)}_seq;
                ALTER TABLE {$this->getTableName(SELF::TBL_SESSION)} 
                    ALTER COLUMN id 
                    SET DEFAULT NEXTVAL('{$this->getTableName(SELF::TBL_SESSION)}_seq')";
        $this->dbExecute($sql);    
    }

    public function createViewSystemUser() {
        $sql = "CREATE OR REPLACE VIEW {$this->getViewName('system_user')} AS 
                    SELECT user_id,user_name,
                           (CASE WHEN user_active THEN 'ACTIVE' ELSE 'DEACTIVE' END) AS user_active
                    FROM {$this->getTableName(SELF::TBL_USER)}";
        $this->dbExecute($sql);
    }

    public function createTblAuthItem() {
        $sql = "CREATE TABLE {$this->getTableName(SELF::TBL_ITEM)} (                     
                    item_id INTEGER PRIMARY KEY,
                    item_type INTEGER,
                    item_name TEXT UNIQUE,
                    item_description TEXT
                );
                CREATE SEQUENCE {$this->getTableName(SELF::TBL_ITEM)}_seq;
                ALTER TABLE {$this->getTableName(SELF::TBL_ITEM)} 
                    ALTER COLUMN item_id 
                    SET DEFAULT NEXTVAL('{$this->getTableName(SELF::TBL_ITEM)}_seq')";
        $this->dbExecute($sql);
    }

    public function createViewAccessRole() {
        $sql = "CREATE OR REPLACE VIEW {{$this->getViewName(self::VIW_ACCESS_ROLE)} AS 
                    SELECT item_id,item_name 
                    FROM {$this->getTableName(SELF::TBL_ITEM)}
                    WHERE item_type = " . AuthType::ACCESS_ROLE . "";
        $this->dbExecute($sql);
    }

    public function createViewAccessObject() {
        $sql = "CREATE OR REPLACE VIEW {$this->getViewName(self::VIW_ACCESS_OBJECT)} AS 
                    SELECT item_id,item_name 
                    FROM {$this->getTableName(SELF::TBL_ITEM)}
                    WHERE item_type = " . AuthType::ACCESS_OBJECT . "";
        $this->dbExecute($sql);
    }

    public function createViewEnduserRole() {
        $sql = "CREATE OR REPLACE VIEW {$this->getViewName(self::VIW_ENDUSER_ROLE)} AS 
                    SELECT item_id,item_name 
                    FROM {$this->getTableName(SELF::TBL_ITEM)}
                    WHERE item_type = " . AuthType::ENDUSER_ROLE . "";
        $this->dbExecute($sql);
    }
    
    public function createViewRole() {
        $sql = "CREATE OR REPLACE VIEW {$this->getViewName(self::VIW_ROLE)} AS 
                    SELECT item_id,item_name,
                   (CASE WHEN item_type = " . AuthType::ENDUSER_ROLE . " THEN 'ENDUSER_ROLE' 
                         WHEN item_type = " . AuthType::ACCESS_ROLE . " THEN 'ACCESS_ROLE' 
                    END) AS item_type
                    FROM {$this->getTableName(SELF::TBL_ITEM)}
                    WHERE item_type IN(" . AuthType::ENDUSER_ROLE . "," . AuthType::ACCESS_ROLE . ")";
        $this->dbExecute($sql);
    }

    public function createTblAuthChild() {
        $sql = "CREATE TABLE {$this->getTableName(SELF::TBL_CHILD)} (                     
                    parent_id INTEGER REFERENCES {$this->getTableName(SELF::TBL_ITEM)}(item_id),
                    child_id INTEGER REFERENCES {$this->getTableName(SELF::TBL_ITEM)}(item_id),
                    PRIMARY KEY(parent_id, child_id)
                )";
        $this->dbExecute($sql);
    }

    public function createTblAuthAssign() {
        $sql = "CREATE TABLE {$this->getTableName(SELF::TBL_ASSIGN)} (                     
                    user_id INTEGER REFERENCES {$this->getTableName(SELF::TBL_USER)}(user_id),
                    item_id INTEGER REFERENCES {$this->getTableName(SELF::TBL_ITEM)}(item_id),
                    PRIMARY KEY(user_id, item_id)
                )";
        $this->dbExecute($sql);
    }

    public function userRemove($user_name) {
        $sql = "DELETE FROM {$this->getTableName(SELF::TBL_USER)} WHERE user_name = '{$user_name}'";
        $this->dbExecute($sql);
    }

    public function createViewObjectRelation() {
        //@todo implement method
        throw new \Exception(__METHOD__. 'not implemented.');
    }

    public function createViewUserRole() {
        //@todo implement method
        throw new \Exception(__METHOD__. 'not implemented.');
    }

}

?>