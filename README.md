# VyVBTSPractica1

## Primer caso de uso: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH

**Comando para hacer la petición:**  
```sh
curl -X GET "s1037905437.mialojamiento.es/user?id=XXX"
```

**En el navegador:**  
```
s1037905437.mialojamiento.es/user?id=XXX
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

**Comando para hacer la petición:**  
```sh
curl -X GET "s1037905437.mialojamiento.es/streams"
```

**En el navegador:**  
```
s1037905437.mialojamiento.es/streams
```

### Posibles errores:
- Token expirado → **Error 401**
- Internal server error → **Error 500**

---

## Tercer caso de uso: CONSULTAR “TOP STREAMS ENRIQUECIDOS”

**Comando para hacer la petición:**  
```sh
curl -X GET "s1037905437.mialojamiento.es/enriched?limit=XXX"
```

**En el navegador:**  
```
s1037905437.mialojamiento.es/enriched?limit=XXX
```

Siendo `XXX` cualquier límite entre `1` y `100`.

### Posibles errores:
- Si no pones la variable `limit` → **Error 400**
- Si la dejas vacía (`enriched?limit=`) → **Error 400**
- Token expirado → **Error 401**
- Internal server error → **Error 500**
