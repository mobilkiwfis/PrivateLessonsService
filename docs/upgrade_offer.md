# Bump offer


### Path:
**/requests/bump_offer.php**


### Request type:
**POST**


### Pre-check:

**session**:
* `E201`, *user* - if user is not logged in


### Parameters (+ necesery, ? optional):

**+ offer_id** : *int* - Offer's id
* `E301` - this = null
* `E310` - this < 0 
* `E321` - offer not exist or doesnt belong to current logged user

### Returns:

##### When succesfull:
**status**: "OK"
**data**: object with data

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array with errors
* `E201`, *not_activated*: Account has not been activated yet
* `E201`, *banned*: Account has been banned