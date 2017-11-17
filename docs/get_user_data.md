# Get user data


### Path:
**/requests/get\_user\_data.php**


### Request type:
**GET**


### Pre-check:

**none**


### Parameters (+ necesery, ? optional):

**? email** : *string* - User's email. If is not set, current logged user email is used instead.
* `E310` - filter_var(this, FILTER_VALIDATE_EMAIL) failed


### Returns:

##### When registered succesfull:
**status**: "OK"
**data**: object with user's data

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array of errors (including following)
* `E301`, *email*: User was not logged in and email param was not set
* `E321`, *email*: Account has not been found in database, user doesnt exist