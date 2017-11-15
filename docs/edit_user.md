# Register


### Path:
**/requests/register.php**


### Request type:
**POST**


### Pre-check:

**session**:
* `E201`, *user* - if user is not logged in


### Parameters (+ necesery, ? optional):

**+ firstname** : *string* - User's firstname
* `E311` - this.length < 1
* `E312` - this.length > 100
* `E310` - match "/[0-9]|[^\w\'\` ]|\_/"

**+ surname** : *string* - User's surname
* `E311` - this.length < 1
* `E312` - this.length > 100
* `E310` - match "/[0-9]|[^\w\'\` ]|\_/"

**+ password** : *string* - User's password
* `E311` - this.length < 6

**+ phone_number** : *string* - User's phone number
* `E310` - this.length != 9
* `E302` - not matches "/[0-9]/"


### Returns:

##### When registered succesfull:
**status**: "OK"
**data**: empty array

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array with errors
* `E301`, *no_fields_to_change*: No fields to change
* `E201`, *not_activated*: Account has not been activated yet
* `E201`, *banned*: Account has been banned