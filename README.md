
# Sistema de votación

Herramienta web para que un administrador someta un punto a votación y contabilice en tiempo real los votos a mano alzada de dos grupos. Cada grupo tiene un peso distinto en el resultado: el Grupo 1 pondera 2/3 y el Grupo 2 pondera 1/3, reflejando una estructura de representación diferenciada. El resultado final se expresa en porcentajes ponderados para las tres categorías posibles: Afirmativo, Abstención y No voto.

## Criterios técnicos

- Una sola página HTML con todo incluido (HTML, CSS y JS en el mismo archivo).
- Bootstrap vía CDN para los estilos, sin CSS custom ni librerías adicionales.
- JavaScript reducido al mínimo indispensable.
- Todo el sistema en español: labels, mensajes de error y resultados.

## Flujo general

El administrador ingresa el texto del punto a votar y los datos de ambos grupos. Al presionar **Confirmar**, se ejecuta la validación (campos requeridos y consistencia de datos). Si todo es válido, se muestra la sección de resultados encabezada por el punto votado, con una tabla de porcentajes y una barra de progreso por categoría. Debajo aparece un botón **Votar de nuevo** que reinicia el ciclo.

## Formulario principal (`index.html`)

### Punto a votar

Campo de texto multilinea, obligatorio.

### Grupos

Dos fieldsets, uno por grupo, cada uno con los siguientes campos:

- **Presentes:** cantidad de personas presentes en el grupo (entero, obligatorio, mínimo 0).
- **Afirmativo:** cantidad de personas que votan afirmativo (entero, obligatorio, mínimo 0).
- **Abstención:** cantidad de personas que se abstienen (entero, obligatorio, mínimo 0).

## Cálculo del resultado

Cada categoría (Afirmativo, Abstención y No voto) se calcula de la misma manera: se obtiene qué fracción de los presentes de cada grupo corresponde a esa categoría, y luego se pondera por el peso del grupo (2/3 para el Grupo 1, 1/3 para el Grupo 2). La suma de los tres porcentajes siempre da 100%.

### Casos especiales

- Si un grupo tiene 0 presentes, no se pondera y todo el peso recae sobre el otro grupo.
- Si ambos grupos tienen 0 presentes, se muestra un error y no se calcula el resultado.

### Formato

Los porcentajes se muestran redondeados a dos decimales. El resultado es puramente visual en pantalla.

## Ejemplo práctico

**Punto a votar:**
> Se propone modificar el reglamento interno para que las reuniones ordinarias
> pasen de frecuencia semanal a frecuencia quincenal, liberando el espacio los
> martes alternos para actividades abiertas a la comunidad.

| | Grupo 1 | Grupo 2 |
|---|---|---|
| Presentes | 12 | 9 |
| Afirmativo | 8 | 3 |
| Abstención | 2 | 4 |
| *No voto* | *2* | *2* |

Al presionar **Confirmar**, el sistema valida los datos y calcula el resultado.

El Grupo 1 tiene el doble de presentes que el Grupo 2 y una mayoría afirmativa clara, mientras que el Grupo 2 está más dividido. Al ponderar por el peso de cada grupo (2/3 y 1/3), el resultado final es:

| Categoría | Cálculo | Resultado |
|---|---|---|
| **Afirmativo** | (8/12 × 2/3) + (3/9 × 1/3) | **55.56%** |
| **Abstención** | (2/12 × 2/3) + (4/9 × 1/3) | **22.22%** |
| **No voto** | (2/12 × 2/3) + (2/9 × 1/3) | **22.22%** |

Debajo de la tabla aparece una barra de progreso por categoría y el botón **Votar de nuevo** para iniciar una nueva votación.

