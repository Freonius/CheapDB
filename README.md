CheapDB
=======

####A basic, unsecure, JSON database from PHP and JavaScript

This is a very basic JSON creator to use if you don't have access to a "real" database.
Of course you can't store sensitive data since it will be visible to anyone accessing the JSON file, but in some cases there is no sensitive data.

Being already in JSON it can interact perfectly with JavaScript.

While the additions or changes to the JSON object require PHP, the selection can be made entirely in JavaScript.

###Usage in PHP:

    $db=new CheapDB("songs", array("song","artist"),"../_json/",true);
    $result=$db->get();

$result, if the json file exists and no other errors were found, would be an associative array where the first key is the id in the "database".
So the element with id 5 and key "song" can be printed by

    echo $result[5]["song"];
  
so it would always be 5, even though elements 1 through 4 do not exist.

The id starts at 1, not 0.

The json file, even if empty, must exist already before using the "database".

To add or modify an entry you must first set the values:

    $db->setValue("song","Miss Atomic Bomb");
    $db->setValue("artist","The Killers");
  
where the first parameter is the key and the second one is the actual value. Do this for each key/value you wish to add.

When ready to create a new entry simply call the method:

    $db->append();
  
which returns true on success, false otherwise.

If you wish to change an entry:

    $db->change(5);
    
where the first and only parameter is the id of the entry you want to change.

To delete an entry just use:

    $db->delete(5);
  
again, with the id of the entry.

To select everything with a specific value for a key use:

    $db->setWhere("artist","The Killers");
    $res=$db->get();
  
which only checks for equality.
To get the entry with a specific id use "id" as a key.

To add the json file to the html use:

    echo $db->getScript();
  
which returns the name of the file with script tags.


###In JavaScript:

First create a new instance of CheapDB.

    var db=new CheapDB();
    
then, select the "database" (which must be included in the page as an external javascript file before calling the setDB method):

    db.setDB("songs");
  
Then select the id:

    var id=1;
    db.selectId(id);
  
Returns true if the id exists, false otherwise.

To select a value:

    var song=db.selectKey("song");
    var artist=db.selectKey("artist");
    
They return false if the key does not exist.
