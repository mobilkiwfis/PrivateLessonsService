# Add offer


### Path:
**/requests/add_offer.php**


### Request type:
**POST**


### Pre-check:

**session**:
* `E201`, *user* - if user is not logged in


### Parameters (+ necesery, ? optional):

**+ description** : *string* - Offer's description
* `E301` - this = null
* `E311` - this.length < 1
* `E312` - this.length > 100

**+ category** : *string* - Offer's category
* `E301` - this = null
* `E320` - not maching category in database

**+ price** : *string / float* - Price per hour
* `E301` - this = null
* `E310` - this <= 0.0 or invaild value

**+ localization** : *string* - User's localization
* `E301` - this = null
* `E311` - this.length < 1
* `E312` - this.length > 256

**+ at_teachers_house** : *bool* - Lessons at teacher's house
* `E301` - this = null

**+ at_students_house** : *bool* - Lessons at students's house
* `E301` - this = null

**+ get_to_student_for_free** : *bool* - No price for comming to student
* `E301` - this = null

**+ mo_morning** : *bool* - Mo moring
* `E301` - this = null

**+ mo_evening** : *bool* - Mo evening
* `E301` - this = null

**+ tu_morning** : *bool* - Tu moring
* `E301` - this = null

**+ tu_evening** : *bool* - Tu evening
* `E301` - this = null

**+ we_morning** : *bool* - We moring
* `E301` - this = null

**+ we_evening** : *bool* - We evening
* `E301` - this = null

**+ th_morning** : *bool* - Th moring
* `E301` - this = null

**+ th_evening** : *bool* - Th evening
* `E301` - this = null

**+ fr_morning** : *bool* - Fr moring
* `E301` - this = null

**+ fr_evening** : *bool* - Fr evening
* `E301` - this = null

**+ sa_morning** : *bool* - Sa moring
* `E301` - this = null

**+ sa_evening** : *bool* - Sa evening
* `E301` - this = null

**+ su_morning** : *bool* - Su moring
* `E301` - this = null

**+ su_evening** : *bool* - Su evening
* `E301` - this = null


### Returns:

##### When succesfull:
**status**: "OK"
**data**: empty array

##### When at least one stage failed:
**status**: "NO_OK" 
**data**: array with errors
* `E201`, *not_activated*: Account has not been activated yet
* `E201`, *banned*: Account has been banned