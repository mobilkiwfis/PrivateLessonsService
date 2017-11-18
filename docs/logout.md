# Logout


### Path:
**/requests/logout.php**


### Request type:
**POST**


### Pre-check:

**session**:
* `E201`, *user* - if user is not logged in


### Parameters (+ necesery, ? optional):

**none**


### Returns:

##### When succesfull:
**status**: "OK"
**data**: empty array

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array of errors