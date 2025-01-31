# Twitch-Analytics
Twitch genera una cantidad masiva de datos valiosos, pero las marcas, creadores y
empresas no siempre saben cómo usarlos. Twitch Analytics transforma esos datos en
insights accionables.
## Primer caso de uso: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH
Este endpoint permite a los clientes consultar la información de un streamer de Twitch
mediante su ID. El sistema realiza una consulta a la API de Twitch para obtener información
detallada del usuario.

**Comando para hacer la petición:**  
```sh
curl -X GET "https://vyvbts.com/user?id=XXX"
```

**En el navegador:**  
```
https://vyvbts.com/user?id=XXX
```

Siendo `XXX` cualquier id válido, como puede ser el `id=1` mismamente.

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
curl -X GET "https://vyvbts.com/streams"
```

**En el navegador:**  
```
https://vyvbts.com/streams
```

### Posibles errores:
- Token expirado → **Error 401**
- Internal server error → **Error 500**

---

## Tercer caso de uso: CONSULTAR “TOP STREAMS ENRIQUECIDOS”
Este caso de uso realiza un filtrado y enriquecimiento del listado de Streams en Vivo.

**Comando para hacer la petición:**  
```sh
curl -X GET "https://vyvbts.com/enriched?limit=XXX"
```

**En el navegador:**  
```
https://vyvbts.com/enriched?limit=XXX
```

Siendo `XXX` cualquier límite entre `1` y `100`.

### Posibles errores:
- Si no pones la variable `limit` → **Error 400**
- Si la dejas vacía (`enriched?limit=`) → **Error 400**
- Token expirado → **Error 401**
- Internal server error → **Error 500**
