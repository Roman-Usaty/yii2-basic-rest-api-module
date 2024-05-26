This is RestApi module for yii2 basic application.

Small documentation for Rest API.

Basic url: domain/v1/site.<br>
Allowed method: GET<br>
Return: string 'This is the api action index'

Url for login: domain/v1/site/login
Allowed method: POST<br>
Required body params:<br>
<pre>
{
    username: string
    password: string
}
</pre>
Return: string Token for authentication must used like
Bearer or Username (in basic auth)

Url: domain/v1/patients<br>
Allowed method: POST, GET, OPTIONS<br>
Required body params:<br>
POST<br>
<pre>
{
    name:            string  Patients full name
    address:         string  Patients address
    birthday:        string  A valid date string
    phone:           string  A valid phone number string
    polyclinic_id:   int     Id of existing polyclynics
    status_id:       int     The status of patients
    treatment_id:    int     Id of the treatment form
    form_disease_id: int     Id of the course of the disease
    diagnosis_date:  string  A valid date string
    recovery_date:   string  A valid date string
    analysis_date:   string  A valid date string
    source_id:       int     User ID from whom the patient was infected
}
</pre>
GET - query search params<br>
example: ?name=`<value>`&phone=`<value>`
<pre>
{
    name:            string  Patients full name
    phone:           int     A valid phone number string
    polyclinic_id:   int     Id of existing polyclynics
    status_id:       int     The status of patients
    treatment_id:    int     Id of the treatment form
    form_disease_id: int     Id of the course of the disease
}
</pre>
OPTIONS<br>
Return allowed method in header field