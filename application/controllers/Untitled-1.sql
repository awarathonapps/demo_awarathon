MANUAL QUERY:



 public function get_manual_score($ assessment_id, $ user_id) { $ query = "SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  @lastrank as last_rank,
            (SELECT 
            CASE 
            WHEN main.overall_score > previous THEN @cnt := @cnt + 1
            WHEN main.overall_score < previous THEN @cnt := @cnt + 1
            WHEN main.overall_score = previous THEN @cnt := @cnt
            ELSE @cnt := 1 END ) as rank,
            ( SELECT 
            CASE 
            WHEN @lastrank=0 THEN @lastrank := @cnt
            WHEN main.overall_score = @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank
            WHEN main.overall_score < @dcp AND @lastrank != @cnt THEN @lastrank := @lastrank + 2
            WHEN main.overall_score < @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank + 1
            END) as final_rank,
            @dcp := main.overall_score AS current
            FROM (
            SELECT ps.user_id, ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(ps.question_id) ) ,2) AS overall_score 
            FROM assessment_results_trans AS ps 
            WHERE ps.user_id='".$ user_id."' AND ps.assessment_id = '".$ assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0, @lastrank := 0 ) AS qcounter) as q
            WHERE q.user_id='".$ user_id."'";

$ result = $ this -> db -> query($ query);

return $ result -> row();

}




 public function get_ai_score($company_id, $assessment_id, $user_id)
    {
        $query = "SELECT q.overall_score as overall_score FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND( IF(SUM(ps.weighted_score)=0, SUM(ps.score)/count(ps.question_id), SUM(ps.weighted_score)/COUNT(DISTINCT ps.question_id) ) ,2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND  ps.assessment_id = '" . $assessment_id . "'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id='" . $user_id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }