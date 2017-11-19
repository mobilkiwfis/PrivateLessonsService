# Get user data


### Path:
**/requests/get\_user\_data.php**


### Request type:
**GET**


### Pre-check:

**none**


### Parameters (+ necesery, ? optional):

**+ offer\_id** : *int* - Offer's id
* `E310` - this = null
* `E310` - this < 0


### Returns:

##### When succesfull:
**status**: "OK"
**data**: object with offers's data

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array of errors (including following)
* `E321`, *offer_id*: Offer with offer_id was not found in database