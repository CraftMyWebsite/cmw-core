Projet CraftMyWebsite - CMS multi-gaming
=================================================

### ATTENTION : Le projet est toujours en stade de développement.

Ce projet a pour ambition de proposer à l'ensemble des administrateurs de serveurs de jeux de mettre à disposition de ses joueurs une plateforme web performante et pertinente.

[![License](https://img.shields.io/badge/License-GNU%20GPL-%239f9f9f)](https://www.gnu.org/licenses/gpl-3.0.fr.html)
[![Latest release](https://img.shields.io/badge/v2.0.0-%234c29cc)](https://github.com/CraftMyWebsite/cmw-core)


Introduction
------------

À ce stade du projet, seul le jeu Minecraft sera utilisable avec CraftMyWebsite et uniquement les langues françaises et anglaises sont disponibles. 

Installation
------------

Attention, sur Nginx uniquement, la configuration suivante du serveur est nécessaire :

```bash
autoindex off;

location / {
  if (!-e $request_filename){
    rewrite ^(.*)$ /%1 redirect;
  }
  if (!-e $request_filename){
    rewrite ^(.*)$ /index.php?url=$1 break;
  }
```

Une fois les fichiers du site téléchargés, accédez à votre site. Vous serez automatiquement redirigé vers l'installateur de CraftMyWebsite.
Pensez à supprimer le dossier `/installation` une fois le CMS configuré s'il n'est pas supprimé automatiquement.


Support, infos et communauté
------------

Liens utiles :
- **Discord**: https://discord.gg/tscRZCU
- **Forum**: https://craftmywebsite.fr/forum
- **Contact**: https://craftmywebsite.fr/contactez/nous
- **Twitter**: https://twitter.com/CraftMyWebsite
- **Demo**: http://demo.craftmywebsite.fr


Nos partenaires
------------

- [WebStrator.fr](https://www.webstrator.fr): Hébergeur web
- [MineStrator.com](https://www.minestrator.com): Hébergeur de serveurs Minecraft
- [Serveurs-minecraft.net](https://www.serveurs-minecraft.net): Référencement de serveurs Minecraft

Copyright
------------

CraftMyWebsite est mis à disposition selon les termes de la [licence Creative Commons Attribution - Pas d'Utilisation Commerciale - Pas de Modification 4.0 International](http://creativecommons.org/licenses/by-nc-nd/4.0/). Fondé(e) sur une œuvre à [https://craftmywebsite.fr/telecharger](http://craftmywebsite.fr/telecharger). Les autorisations au-delà du champ de cette licence peuvent être obtenues à [https://craftmywebsite.fr/cgu](https://craftmywebsite.fr/cgu/).

Copyright © CraftMyWebsite 2014-2022 
