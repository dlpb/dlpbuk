<?php 
    include_once("data/data.php");
?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Daniel Brown</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link href="/style/style.css" rel="stylesheet">

  </head>

  <body id="page-top">

    <!-- Header -->
    <header class="masthead d-flex">
      <div class="container text-center my-auto header-shaded">
        <h1 class="mb-1">Daniel Brown</h1>
        <h3 class="mb-5">
          <em>Software Engineer</em>
        </h3>
        <a class="btn btn-light btn-xl js-scroll-trigger" href="#about">Find Out More</a>
      </div>
    </header>
    
    <?php
        $data = getData("data");
        foreach($data as $filename => $contents){
            echo renderSection($filename, $contents);
        }
        function renderContentsAsUnstyledPrimaryList(stdClass $contents): string {
            $html = "";
            $html = $html."<ul class='list-unstyled'>";
            foreach($contents->data as $data){
                $header = NULL;
                $name = NULL;
                $url = NULL;
                $link_text = NULL;
                $badges = NULL;
                $href = NULL;
                $urlStyle = NULL;

                if(isset($data->header)){
                    $header = htmlspecialchars($data->header);
                }
                if(isset($data->name)){
                    $name = htmlspecialchars($data->name);
                }
                if(isset($data->url)){
                    $url = htmlspecialchars($data->url);
                }
                if(isset($data->url_style)){
                    $urlStyle = htmlspecialchars($data->url_style);
                }
                if(isset($data->link_text)){
                    $link_text = htmlspecialchars($data->link_text);
                }
                if(isset($data->image)){
                    //images not supported
                }
                if(isset($data->badges)){
                    foreach($data->badges as $badge){
                        $badges = $badges."<span class='badge badge-dark'>".htmlspecialchars($badge)."</span> ";
                    }
                }
                if(!is_null($url) && !is_null($link_text)){
                    $href = "<a class='$urlStyle' target='blank' href='".htmlspecialchars($url)."'>".htmlspecialchars($link_text)."</a>";
                }
                $html = $html."<li><h2>$header</h2>$name $href $badges</li>";
            }
            $html = $html."</ul>";
            return $html;
        }
        function renderContentsAsUnstyledList(stdClass $contents): string {
            $html = "";
            $html = $html."<ul class='list-unstyled'>";
            foreach($contents->data as $data){
                $name = NULL;
                $url = NULL;
                $link_text = NULL;
                $badges = NULL;
                $href = NULL;
                $urlStyle = NULL;

                if(isset($data->name)){
                    $name = htmlspecialchars($data->name);
                }
                if(isset($data->url)){
                    $url = htmlspecialchars($data->url);
                }
                if(isset($data->url_style)){
                    $urlStyle = htmlspecialchars($data->url_style);
                }
                if(isset($data->link_text)){
                    $link_text = htmlspecialchars($data->link_text);
                }
                if(isset($data->image)){
                    //images not supported
                }
                if(isset($data->badges)){
                    foreach($data->badges as $badge){
                        $badges = $badges."<span class='badge badge-dark'>".htmlspecialchars($badge)."</span> ";
                    }
                }
                if(!is_null($url) && !is_null($link_text)){
                    $href = "<a class='$urlStyle' target='blank' href='".htmlspecialchars($url)."'>".htmlspecialchars($link_text)."</a>";
                }
                
                $html = $html."<li>$name $href $badges</li>";
            }
            $html = $html."</ul>";
            return $html;
        }
        function renderContentsAsCard(stdClass $contents): string {
            $html = "";
            //style max width 100
            $html = $html."<div class='row mr-0' >";
            foreach($contents->data as $data){
                $name = NULL;
                $badges = NULL;
                $img = NULL;
                $href = NULL;
                if(isset($data->name)){
                    $name = htmlspecialchars($data->name);
                }
                if(isset($data->urls)){
                    $href="<ul class='list-unstyled'>";
                    foreach($data->urls as $url){
                        $urll = htmlspecialchars($url->url);
                        $link_text = htmlspecialchars($url->link_text);
                        $href = $href."<li><a href='$urll' target='blank'>$link_text</a></li>";
                    }
                    $href = $href."</ul>";
                }
                else {
                    $url = "";
                    $link_text = "";
                    if(isset($data->url)){
                        $url = htmlspecialchars($data->url);
                    }
                    if(isset($data->link_text)){
                        $link_text = htmlspecialchars($data->link_text);
                    }
                    $href = "<p><a href='$url' target='blank'>$link_text</a></p>";
                }
                if(isset($data->image)){
                    $image = $data->image;
                    if(isset($image->url) && isset($image->alt_text)){
                        $width = "";
                        $height = "";
                        if(isset($image->width)){
                            $width = "width='".htmlspecialchars($image->width)."'";
                        }
                        if(isset($image->height)){
                            $height = "height='".htmlspecialchars($image->height)."'";
                        }
                        $img = "<img class='icon' src='".htmlspecialchars($image->url)."' $width $height alt='".htmlspecialchars($image->alt_text)."'>";
                    }
                }
                if(isset($data->badges)){
                    foreach($data->badges as $badge){
                        $badges = $badges."<span class='badge badge-dark'>".htmlspecialchars($badge)."</span> ";
                    }
                }
                $html = $html."<div class='col-lg-3 col-md-6 mb-5 mb-lg-0'>";
                $html = $html.$img;
                $html = $html."<h4>";
                $html = $html."<strong>$name</strong>";
                $html = $html."</h4>";
                $html = $html.$href;
                $html = $html."<p>$badges</p>";
             
                $html = $html."</div>";
            }
            $html = $html."</div>";
            return $html;
        }
        function renderContentAsInclude(stdClass $contents): string {
            $html = "";
            //style max width 100
            foreach($contents->data as $data){
                $url = NULL;
                $frameborder = "0";
                $scrolling="no";
                $style="width:100%; height:30em";
               
                if(isset($data->url)){
                    $url = htmlspecialchars($data->url);
                }
                if(isset($data->style)){
                    $style = htmlspecialchars($data->style->style);
                    $scrolling = htmlspecialchars($data->style->scrolling);
                    $frameborder = htmlspecialchars($data->style->frameborder);
                }
                
                $html = $html."<iframe src='$url' style='$style' frameborder='$frameborder' scrolling='$scrolling'></iframe>";
            }
            return $html;
        }
        function renderSection(string $sectionName, ?stdClass $contents): string {
            $html = "";
            if(!is_null($contents)){
                $sectionMetadata = $contents->section;
                $style = $contents->style;
                
                $html = $html."<section class='content-section ".htmlspecialchars($style->class)."' id='".htmlspecialchars($sectionMetadata->id)."'>";
               
                $html = $html."<!--".htmlspecialchars($sectionMetadata->id)."-->";
                $html = $html."<div class='container text-center'>";
                $html = $html."<div class='content-section-heading'>";
                $html = $html."<h2 class='mb-5'>".ucwords( htmlspecialchars($sectionMetadata->title))."</h2>";
                $html = $html."</div>";
                $html = $html."<div class='row'>";
                switch($style->layout){
                    case "list-primary-unstyled":
                        if(isset($style->grouping) && $style->grouping){
                            $groupedHtml = "";
                            foreach($contents->data as $key=>$content){
                                $groupedHtml = $groupedHtml."<div class='col-lg-10 mx-auto'>";
                                $groupedHtml = $groupedHtml."<h2>$key</h2>";
                                $groupedHtml = $groupedHtml.renderContentsAsUnstyledPrimaryList($content);
                                $groupedHtml = $groupedHtml."</div>";
                            }
                            $html = $html. $groupedHtml;
                        }
                        else {
                            $html = $html."<div class='col-lg-10 mx-auto'>";
                            $html = $html.renderContentsAsUnstyledPrimaryList($contents);
                            $html = $html."</div>";
                        }
                        break;
                    case "list-unstyled":
                        if(isset($style->grouping) && $style->grouping){
                            $groupedHtml = "";
                            foreach($contents->data as $key=>$content){
                                $groupedHtml = $groupedHtml."<div class='col-lg-3 col-md-6 mb-5 mb-lg-0'>";
                                $groupedHtml = $groupedHtml."<h2>$key</h2>";
                                $groupedHtml = $groupedHtml.renderContentsAsUnstyledList($content);
                                $groupedHtml = $groupedHtml."</div>";
                            }
                            $html = $html. $groupedHtml;
                        }
                        else {
                            $html = $html.renderContentsAsUnstyledList($contents);
                        }
                        break;
                    
                    case "card":
                        $html = $html.renderContentsAsCard($contents);
                        break;
                    case "include":
                        $html = $html.renderContentAsInclude($contents);
                        break;
                    
                }
            }
            $html = $html."</div>";
            $html = $html."</div>";

            $html = $html."</section>";
            return $html;
        }
    ?>

    <!-- Footer -->
    <footer class="footer text-center">
      <div class="container">        
        <p class="text-muted small mb-0">Copyright &copy; dlpb.uk 2018-<?php echo date("Y")?>. This is a personal website and any views or opinions expressed within are my own.</p>
      </div>
    </footer>
  </body>

</html>
