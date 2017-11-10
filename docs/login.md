# Register


### Path:
**/requests/login.php**


### Request type:
**POST**


### Pre-check:

**session**:
* `E202` - if user is already logged


### Parameters (+ necesery, ? optional):

**+ email** : *string* - User's email
* `E301` - this = null
* `E310` - filter_var(this, FILTER_VALIDATE_EMAIL) failed

**+ password** : *string* - User's password
* `E301` - this = null


### Returns:

##### When registered succesfull:
**status**: "OK"
**data**: empty array

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array of errors (including following)
* `E300`, *user*: No matching email and password in database
* `E201`, *not_active*: Account has not been activated yet
* `E201`, *banned*: Account has been banned