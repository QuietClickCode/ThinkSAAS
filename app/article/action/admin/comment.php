<?php
defined('IN_TS') or die('Access Denied.');

switch ($ts){

    case "list":

        $userid = intval($_GET['userid']);
        $articleid = intval($_GET['articleid']);

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $url = SITE_URL.'index.php?app=article&ac=admin&mg=comment&ts=list&page=';
        $lstart = $page*10-10;

        $where = null;

        if($userid){
            $where = array(
                'userid'=>$userid,
            );
        }

        if($articleid){
            $where = array(
                'articleid'=>$articleid,
            );
        }

        $arrComment = $new['article']->findAll('article_comment',$where,'addtime desc',null,$lstart.',10');

        $commentNum = $new['article']->findCount('article_comment',$where);

        $pageUrl = pagination($commentNum, 10, $page, $url);

        include template("admin/comment_list");

        break;


    case "delete":

        $commentid = intval($_GET['commentid']);

        $strComment = $new['article']->find('article_comment',array(
            'commentid'=>$commentid,
        ));

        $new['article']->delete('article_comment',array(
            'commentid'=>$commentid,
        ));

        #统计评论数
        $count_comment = $new['article']->findCount('article_comment',array(
            'articleid'=>$strComment['articleid'],
        ));

        //更新评论数
        $new['article']->update('article',array(
            'articleid'=>$strComment['articleid'],
        ),array(
            'count_comment'=>$count_comment,
        ));


        #处理积分
        aac('user') -> doScore($TS_URL['app'], $TS_URL['ac'], $TS_URL['ts'],$strComment['userid'],$TS_URL['mg']);

        qiMsg('删除成功');

        break;


}