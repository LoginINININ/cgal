// Copyright (c) 2003,2004  INRIA Sophia-Antipolis (France) and
// Notre Dame University (U.S.A.).  All rights reserved.
//
// This file is part of CGAL (www.cgal.org); you may redistribute it under
// the terms of the Q Public License version 1.0.
// See the file LICENSE.QPL distributed with CGAL.
//
// Licensees holding a valid commercial license may use this file in
// accordance with the commercial license agreement provided with the software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING THE
// WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.
//
// $Source$
// $Revision$ $Date$
// $Name$
//
// Author(s)     : Menelaos Karavelas <mkaravel@cse.nd.edu>




#ifndef CGAL_SEGMENT_VORONOI_DIAGRAM_VERTEX_BASE_2_H
#define CGAL_SEGMENT_VORONOI_DIAGRAM_VERTEX_BASE_2_H


#include <CGAL/Triangulation_ds_vertex_base_2.h>
#include <CGAL/Segment_Voronoi_diagram_storage_site_2.h>

CGAL_BEGIN_NAMESPACE


template < class Gt, class PC,
	   class Vb = Triangulation_ds_vertex_base_2<> >
class Segment_Voronoi_diagram_vertex_base_2
  : public Vb
{
private:
  typedef typename Vb::Triangulation_data_structure  DS;
public:
  // TYPES
  //------
  typedef Gt                                  Geom_traits;
  typedef PC                                  Point_container;
  typedef typename Point_container::iterator  Point_handle;
  typedef Vb                                  Base;
  typedef typename Gt::Site_2                 Site_2;

  typedef
  Segment_Voronoi_diagram_storage_site_2<Gt,Point_handle>
  Storage_site_2;

  typedef DS           Segment_Voronoi_diagram_data_structure_2;
  
  typedef typename DS::Face_handle    Face_handle;
  typedef typename DS::Vertex_handle  Vertex_handle;


  template < typename DS2 >
  struct Rebind_TDS {
    typedef typename Vb::template Rebind_TDS<DS2>::Other      Vb2;
    typedef Segment_Voronoi_diagram_vertex_base_2<Gt,PC,Vb2>  Other;
  };

  
  Segment_Voronoi_diagram_vertex_base_2 () : Vb(), ss_() {}
    
  Segment_Voronoi_diagram_vertex_base_2(const Storage_site_2& ss,
					Face_handle f)
    : Vb(f), ss_(ss)  {}

  void set_site(const Storage_site_2& ss) { ss_ = ss; }

  const Storage_site_2& storage_site() const { return ss_; }
  Site_2                site()         const { return ss_.site(); }

  bool is_segment() const { return ss_.is_segment(); }
  bool is_point()   const { return ss_.is_point(); }

  //the following trivial is_valid to allow
  // the user of derived face base classes 
  // to add their own purpose checking
  bool is_valid(bool /* verbose */ = false, int /* level */ = 0) const
  { return true; }

private:
  Storage_site_2 ss_;
  //  std::list<Vb>  adjseg_list; // list of adjacent segments; this is
  // important when I want to do deletions
  //  bool _is_point; // false if it is a segment; true otherwise
  // to be used for intersecting segments:
  //  bool is_simple;
};


CGAL_END_NAMESPACE 

#endif // CGAL_SEGMENT_VORONOI_DIAGRAM_VERTEX_BASE_2_H
