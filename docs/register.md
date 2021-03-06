# Register


### Path:
**/requests/register.php**


### Request type:
**POST**


### Pre-check:

**session**:
* `E202`, *user* - if user is already logged


### Parameters (+ necesery, ? optional):

**+ firstname** : *string* - User's firstname
* `E301` - this = null
* `E311` - this.length < 1
* `E312` - this.length > 100
* `E310` - match "/[0-9]|[^\w\'\` ]|\_/"

**+ surname** : *string* - User's surname
* `E301` - this = null
* `E311` - this.length < 1
* `E312` - this.length > 100
* `E310` - match "/[0-9]|[^\w\'\` ]|\_/"

**+ password** : *string* - User's password
* `E301` - this = null
* `E311` - this.length < 6

**+ email** : *string* - User's email
* `E301` - this = null
* `E310` - filter_var(this, FILTER_VALIDATE_EMAIL) failed
* `E320` - already in database


### Returns:

##### When succesfull:
**status**: "OK"
**data**: empty array

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array with errors