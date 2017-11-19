# Get user data


### Path:
**/requests/get\_user\_data.php**


### Request type:
**GET**


### Pre-check:

**none**


### Parameters (+ necesery, ? optional):

**? user\_id** : *int* - User's id. If is not set, current logged user_id is used instead
* `E310` - filter_var(this, FILTER_VALIDATE_EMAIL) failed

**? email** : *string* - User's email. If is not set, current logged user email is used instead
* `E310` - this < 0

Note: As there are both parameters given, both are checked but only user_id is used.


### Returns:

##### When succesfull:
**status**: "OK"
**data**: object with user's data

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array of errors (including following)
* `E301`, *user*: User was not logged in, and there were not set user\_id and email either
* `E321`, *user*: User with user_id or email was not found in database