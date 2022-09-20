# __API ECF 2022 BACK__

Réalisé en **PHP vanilla**.

```bash

git clone https://github.com/math-dev-24/ECF_STUDI_2022_BACK.git
cd ECF_STUDI_2022_BACK

```

---

### __Routes :__

#### Méthode GET :

##### **Request :**
GET :  `V1/partner`

**Response :**
```js
[
	id: number
	user_id: number
	user_name: str
	email: str
	partner_name: str
	partner_active: str
	logo_url: str
]
```
___
##### **Request :**
GET : `V1/partner/:id`

**Response :**
```js

[
	partner_id: number
	user_id: number
	user_name: str
	user_email: str
	user_active: number
	partner_name: str
	logo_url: str
	partner_active: number
	gestion : [
		v_vetement: number
		v_boisson: number
		c_particulier: number
		c_crosstrainning: number
		c_iplate: number
	],
	struct: [
	[
		id: number
		struct_name: str
		struct_active: number
		gestion_id: number
		v_vetement: number
		v_boisson: number
		c_particulier: number
		c_crosstrainning: number
		c_iplate: number
	],
	...
	]
]
```
---
##### **ReQuest  :**
GET : `V1/struct`

**Response :**
```js
[
	id: number
	struct_name: str
	struct_active: number
	partner_id: number
	partner_user_id: number
	partner_name: str
	logo_url: str
	user_id: number
	email: str
	user_name: str
	user_active: number
]
```
---
##### **Request :**
GET : `V1/struct/:id`

**Response :**
```js
[
	  "struct_id": number
	  "struct_name": str
	  "struct_active": number
	  "partner_id": number
	  "partner_user_id": number
	  "partner_name": str
	  "partner_active": 1,
	  "user_id": number
	  "user_name": str
	  "user_email": str
	  "user_active": number
	  "gestion": {
	    "v_vetement": number
	    "v_boisson": number
	    "c_particulier": number
	    "c_crosstrainning": number
	    "c_pilate": number
]
```
---
#### Méthode PUT :

##### **Request :**
PUT : `V1/partner`

##### **Request :**
PUT : `V1/partner/droit`

##### **Request :**
PUT : `V1/partner/active`

##### **Request :**
PUT : `V1/struct`

##### **Request :**
PUT : `V1/struct/droit`

##### **Request :**
PUT : `V1/struct/active`

##### **Request :**
PUT : `V1/user/:nameColumn`

#### Méthode POST :

##### **Request :**
`V1/login`

##### **Request :**
`V1/partner`

##### **Request :**
`V1/struct`

#### Méthode DELETE :

##### **Request :**
`V1/partner/:id`

##### **Request :**
`V1/struct/:id`
