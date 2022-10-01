# API ECF 2022 BACK

___

Réalisé dans le cadre de l'ECF.

Stack : PHP VANILLA

___

### Table of content : 

1. [Méthode GET](#mthode-get-)
2. [Méthode POST](#mthode-post-)
3. [Méthode PUT](#mthode-put-)
4. [Méthode DELETE](#mthode-delete-)
5. [Télécharger le projet](#tlcharger-le-projet-)

___



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
##### **Request  :**
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

*voir GET partner*

##### **Request :**
PUT : `V1/partner/droit`

##### **Request :**
PUT : `V1/partner/active`

##### **Request :**
PUT : `V1/struct`

*voir GET struct*

##### **Request :**
PUT : `V1/struct/droit`

##### **Request :**
PUT : `V1/struct/active`

##### **Request :**
PUT : `V1/user/:nameColumn`

#### Méthode POST :

##### **Request :**
`V1/login`

```ts
{
    accessToken: string
    user: {
        id: number
        email : string
        first_connect: boolean
        is_admin: boolean
        user_active: boolean
        user_name: string
        profil_url: string
    }
}

```

##### **Request :**
`V1/partner`

##### **Request :**
`V1/struct`

#### Méthode DELETE :

##### **Request :**
`V1/partner/:id`

##### **Request :**
`V1/struct/:id`


___

### Télécharger le projet ?

__Marche à suivre :__

```bash

git clone https://github.com/math-dev-24/ECF_STUDI_2022_BACK.git
cd ECF_STUDI_2022_BACK

```