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
```ts
{
    id: number
    user_id: number
    user_name: string
    email: string
    partner_name: string
    partner_active: string
    logo_url: string
}
```
___
##### **Request :**
GET : `V1/partner/:id`

**Response :**
```ts

{
    partner_id: number
    user_id: number
    user_name: string
    user_email: string
    user_active: number
    profil_url: string
    partner_name: string
    logo_url: string
    partner_active: number
    gestion : {
        v_vetement: number
        v_boisson: number
        c_particulier: number
        c_crosstrainning: number
        c_pilate: number
    }
    struct: {
        {
            id: number
            struct_name: string
            struct_active: number
            gestion_id: number
            v_vetement: number
            v_boisson: number
            c_particulier: number
            c_crosstrainning: number
            c_iplate: number
        }
        ...
    }
}
```
---
##### **ReQuest  :**
GET : `V1/struct`

**Response :**
```ts
{
    id: number
    struct_name: string
    struct_active: number
    partner_id: number
    partner_user_id: number
    partner_name: string
    logo_url: string
    user_id: number
    profil_url: string
    email: string
    user_name: string
    user_active: number
}
```
---
##### **Request :**
GET : `V1/struct/:id`

**Response :**
```ts
{
	  struct_id: number
	  struct_name: string
	  struct_active: number
	  partner_id: number
	  partner_user_id: number
	  partner_name: string
	  partner_active: number
	  user_id: number
	  user_name: string
	  user_email: string
      profil_url: string
	  user_active: number
	  gestion: {
	    v_vetement: number
	    v_boisson: number
	    c_particulier: number
	    c_crosstrainning: number
	    c_pilate: number
}
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
