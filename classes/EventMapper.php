<?php
class EventMapper extends Mapper
{
    public function getEvents() {
        $sql = "SELECT id, start_date, end_date, event_name, description, url_link
            from events";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return json_encode($results);
    }


}