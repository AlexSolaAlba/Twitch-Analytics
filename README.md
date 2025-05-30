# Twitch-Analytics
Twitch genera una cantidad masiva de datos valiosos, pero las marcas, creadores y
empresas no siempre saben cómo usarlos. Twitch Analytics transforma esos datos en
insights accionables.
## Entorno de desarrollo local
Hasta 7/3/2025 habiamos trabajado directamente desplegando las funcionalidades en el dominio público.
Nos hemos dado cuenta de que esto nos ha servido porque hasta ahora ha sido un proyecto simple, pero, no tiene sentido 
realizar las pruebas y el desarrollo en la pagina web. Por ello, a partir de ahora hemos decidido llevarlo
a cabo de manera local. Aquí estan los pasos que debe realizar cada miembro del equipo para desarrollar un 
proyecto:
1. Instalar XAMPP en tu ordenador.
2. Descargar los archivos a modificar, introducir los nuevos archivos y los modificados en un mismo directorio.
3. Arrastrar el directorio a htdocs en XAMPP.
4. Buscar en el navegador "localhost:puerto/directorio/archivo.php" para ver los cambios.
5. Hacer el despliegue en el entorno web.
## Primer caso de uso: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH
Este endpoint permite a los clientes consultar la información de un streamer de Twitch
mediante su ID. El sistema realiza una consulta a la API de Twitch para obtener información
detallada del usuario.

**Comando para hacer la petición:**  
```sh
curl -X GET "https://vyvbts.com/analytics/user?id=XXX" -H "Authorization: Bearer ********************************"
```

**En el navegador:**  
```
https://vyvbts.com/analytics/user?id=XXX
```

**En local:**  
```
localhost/Twitch-Analytics-main/user.php?id=XXX
```

Siendo `XXX` cualquier id válido, como puede ser el `id=1` mismamente.
Siendo `********************************` un token de usuario valido.

### Posibles errores:
- Si no pones la variable `id` → **Error 400**
- Si la dejas vacía (`user?id=`) → **Error 400**
- Si pones un id que no existe como `1900000009` → **Error 404**
- Token expirado → **Error 401**
- Internal server error → **Error 500**

---

## Segundo caso de uso: CONSULTAR STREAMS EN VIVO
Este endpoint permite a los clientes obtener una lista de los streams que están actualmente
en vivo en Twitch. El sistema consulta la API de Twitch utilizando un token de acceso válido.

**Comando para hacer la petición:**  
```sh
curl -X GET "https://vyvbts.com/analytics/streams" -H "Authorization: Bearer ********************************"
```

**En el navegador:**  
```
https://vyvbts.com/analytics/streams
```

**En local:**  
```
localhost/Twitch-Analytics-main/streams.php 
```

Siendo `********************************` un token de usuario valido.

### Posibles errores:
- Token expirado → **Error 401**
- Internal server error → **Error 500**

---

## Tercer caso de uso: CONSULTAR “TOP STREAMS ENRIQUECIDOS”
Este caso de uso realiza un filtrado y enriquecimiento del listado de Streams en Vivo.

**Comando para hacer la petición:**  
```sh
curl -X GET "https://vyvbts.com/analytics/enriched?limit=XXX" -H "Authorization: Bearer ********************************"
```

**En el navegador:**  
```
https://vyvbts.com/analytics/enriched?limit=XXX
```

**En local:**  
```
localhost/Twitch-Analytics-main/enriched.php?limit=XXX
```

Siendo `XXX` cualquier límite entre `1` y `100`.
Siendo `********************************` un token de usuario valido.

### Posibles errores:
- Si no pones la variable `limit` → **Error 400**
- Si la dejas vacía (`enriched?limit=`) → **Error 400**
- Token expirado → **Error 401**
- Internal server error → **Error 500**

---

## REGISTRO DE USUARIOS
El registro de usaurio recibe un email y genera una API Key única, despues devuelve la API Key al usuario.


**Comando para hacer la petición:**  
```sh
curl -X POST https://vyvbts.com/analytics/register.php -d "email=XXX"
```

**En local:**  
Hay que cambiar conexión a la BBDD como por ejemplo: $conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
```
curl -X POST http://localhost/Twitch-Analytics-main/register.php -d "email=XXX"
```

Siendo `XXX` un email cualquiera.

### Posibles errores:
- Si no pones la variable `email` → **Error 400**
- Si la dejas la variable `email` vacía → **Error 400**
- Si la variable `email` es invalida → **Error 400**
- Internal server error → **Error 500**

---

## OBTENCION DE TOKEN
El programa recibe un email y una API Key y genera un token unico, despues devuelve el token al usuario.


**Comando para hacer la petición:**  
```sh
curl -X POST https://vyvbts.com/analytics/token.php -d "email=XXX&api_key=XXX2"
```

**En local:**  
Hay que cambiar conexión a la BBDD como por ejemplo: $conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
```
curl -X POST http://localhost/Twitch-Analytics-main/token.php -d "email=XXX&api_key=XXX2"
```

Siendo `XXX` un email valido.
Siendo `XXX2` una api_key valida.
### Posibles errores:
- Si no pones la variable `email` o `api_key`→ **Error 400**
- Si la dejas la variable `email` o `api_key` vacía → **Error 400**
- Si la variable `email` es invalida → **Error 400**
- Si la variable `api_key` es invalida → **Error 401**
- Internal server error → **Error 500**

---

## CONSULTAR VIDEOS MÁS VISTOS
El programa muestra los datos de los videos más vistos de las categorías más populares.

**Comando para hacer la petición:**  
```sh
curl -X GET "https://vyvbts.com/analytics/topsofthetops?since=XXX" -H "Authorization: Bearer ********************************"
```

**En el navegador:**  
```
https://vyvbts.com/analytics/topsofthetops?since=XXX
```

**En local:**  
```
localhost/Twitch-Analytics-main/topsofthetops.php?since=XXX
```

Siendo `XXX` un número mayor que `0`. Este parámetro es opcional y por defecto estará inicializado en `600`, en caso de que no se utilice.
Siendo `********************************` un token de usuario valido.

### Posibles errores:
- Si dejas la variable `since` vacía (`topsofthetops?since=`) → **Error 400**
- Si no hay datos sobre los videos → **Error 404**
- Token expirado → **Error 401**
- Internal server error → **Error 500**
