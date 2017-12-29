# Hide offer


### Path:
**/requests/hide_offer.php**


### Request type:
**POST**


### Pre-check:

**session**:
* `E201`, *user* - if user is not logged in


### Parameters (+ necesery, ? optional):

**+ offer_id** : *int* - Offer's id
* `E301` - this = null
* `E321` - offer not exist

### Returns:

##### When succesfull:
**status**: "OK"
**data**: object: is_active (bool) - current offer status

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array with errors
* `E201`, *not_activated*: Account has not been activated yet
* `E201`, *banned*: Account has been banned
* `E200`, *offer_not_belongs_to_user*: Offer does not belong to logged user
* `E200`, *offer_cannot_be_bumped_yet*: There is more than one week till offer expires