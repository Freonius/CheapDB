/**
 * @class CheapDB()
 * @author Freonius
 * Select a value from the JSON CheapDB.
 * The methods include setDB(db_name),selectId(id),selectKey(key) and copy()
 */
CheapDB=function(){
	this.name="none";
	this.error=false;
	this.id=0;
	this.res={};
	this.key="";
};
/**
 * Set the name of the database.
 * Returns false if object is not found.
 * @param name {String} name of the database
 * @returns {Boolean}
 */
CheapDB.prototype.setDB=function(name){
	this.name=name+"_json";
	if(typeof(window[this.name])==='undefined'){
		this.error=true;
		return false;
	}
	else{
		return true;
	}
};
/**
 * Set the id of the object in the database. Returns true if object is found
 * and if no errors were encountered before.
 * @param id {Int} Id of object in the database.
 * @returns {Boolean}
 */
CheapDB.prototype.selectId=function(id){
	if(this.error){
		return false;
	}
	else{
		if(!window[this.name].hasOwnProperty(id)){
			return false;
		}
		else{
			this.res=window[this.name][id];
			return true;
		}
	}
};
/**
 * Get the value of key in database at given id.
 * @param key {String} Name of the key in the database
 * @returns {False/String} Returns false if nothing is found, string otherwise.
 */
CheapDB.prototype.selectKey=function(key){
	if(this.res){
		if(this.res.hasOwnProperty(key)){
			return this.res[key];
		}
		return false;
	}
	else{
		return false;
	}
};
CheapDB.prototype.copy=function(){
	var t=this;
	var copy=new Object(t);
	return copy;
};