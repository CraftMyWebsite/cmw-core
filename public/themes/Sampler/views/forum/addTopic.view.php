<?php
/** @var \CMW\Entity\Forum\ForumEntity $forum */


use CMW\Manager\Security\SecurityManager;

$title = "Ajouter un topic | {$forum->getName()}";
$description = "Ajoutez un topic au forum {$forum->getName()}";
?>

<section class="container px-4 px-lg-5 h-100" style="margin-top: 80px">
   <form action="" method="post">
       <?php (new SecurityManager())->insertHiddenToken() ?>

       <div class="align-items-center">
           <h1 class="text-dark font-weight-bold">Ajouter un topic au forum <?= $forum->getName() ?></h1>
           <hr class="divider" />

           <label> Nom du topic
               <input type="text" name="name" required>
           </label>

           <br>

           <label> Contenu
               <textarea name="content" required></textarea>
           </label>

           <br>

           <label> Tags
               <input type="text" name="tags">
           </label>
           <small>TIPS: Si vous voulez plusieurs tags, vous pouvez les séparer en tapant ','</small>

           <br>

           <label> Désactiver les réponses
               <input type="checkbox" name="disallow_replies" value="1">
           </label>

           <br>

           <label> Topic important ?
               <input type="checkbox" name="important" value="1">
           </label>

           <br>

           <button type="submit">Envoyer</button>

       </div>
   </form>

</section>